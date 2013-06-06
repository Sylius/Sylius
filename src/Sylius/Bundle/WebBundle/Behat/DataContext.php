<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\WebBundle\Behat;

use Behat\Behat\Context\BehatContext;
use Behat\Gherkin\Node\TableNode;
use Behat\Symfony2Extension\Context\KernelAwareInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Faker\Factory as FakerFactory;
use Sylius\Bundle\AddressingBundle\Model\ZoneInterface;
use Sylius\Bundle\CoreBundle\Entity\User;
use Sylius\Bundle\ShippingBundle\Calculator\DefaultCalculators;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Locale\Locale;
use Symfony\Component\PropertyAccess\StringUtil;

/**
 * Data writing and reading context.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class DataContext extends BehatContext implements KernelAwareInterface
{
    /**
     * Faker.
     *
     * @var Generator
     */
    private $faker;

    /**
     * Created orders.
     *
     * @var OrderInterface[]
     */
    private $orders;

    public function __construct()
    {
        $this->orders = array();
        $this->faker = FakerFactory::create();
    }

    /**
     * {@inheritdoc}
     */
    public function setKernel(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    /**
     * @Given /^there are following taxonomies defined:$/
     */
    public function thereAreFollowingTaxonomies(TableNode $table)
    {
        $manager = $this->getEntityManager();

        foreach ($table->getHash() as $data) {
            $this->thereIsTaxonomy($data['name']);
        }
    }

    /**
     * @Given /^I created taxonomy "([^""]*)"$/
     */
    public function thereIsTaxonomy($name)
    {
        $taxonomy = $this->getRepository('taxonomy')->createNew();
        $taxonomy->setName($name);

        $this->getEntityManager()->persist($taxonomy);
        $this->getEntityManager()->flush();
    }

    /**
     * @Given /^taxonomy "([^""]*)" has following taxons:$/
     */
    public function taxonomyHasFollowingTaxons($taxonomyName, TableNode $taxonsTable)
    {
        $taxonomy = $this->findOneByName('taxonomy', $taxonomyName);
        $manager = $this->getEntityManager();

        $taxons = array();

        foreach ($taxonsTable->getRows() as $node) {
            $taxonList = explode('>', $node[0]);
            $parent = null;

            foreach ($taxonList as $taxonName) {
                $taxonName = trim($taxonName);

                if (!isset($taxons[$taxonName])) {
                    $taxon = $this->getRepository('taxon')->createNew();
                    $taxon->setName($taxonName);

                    $taxons[$taxonName] = $taxon;
                }

                $taxon = $taxons[$taxonName];

                if (null !== $parent) {
                    $parent->addChild($taxon);
                } else {
                    $taxonomy->addTaxon($taxon);
                }

                $parent = $taxon;
            }
        }

        $manager->persist($taxonomy);
        $manager->flush();
    }

    /**
     * @Given /^there are following users:$/
     */
    public function thereAreFollowingUsers(TableNode $table)
    {
        $manager = $this->getEntityManager();

        foreach ($table->getHash() as $data) {
            $this->thereIsUser(
                $data['email'],
                isset($data['password']) ? $data['password'] : $this->faker->word(),
                'ROLE_USER',
                isset($data['enabled']) ? $data['enabled'] : true,
                $data['address']
            );
        }
    }

    public function thereIsUser($email, $password, $role = null, $enabled = 'yes', $address = null)
    {
        $addressData = explode(',', $address);
        $addressData = array_map('trim', $addressData);

        $user = new User();

        $user->setFirstname($this->faker->firstName);
        $user->setLastname($this->faker->lastName);
        $user->setFirstname(null === $address ? $this->faker->firstName : $addressData[0]);
        $user->setLastname(null === $address ? $this->faker->lastName : $addressData[1]);
        $user->setEmail($email);
        $user->setEnabled('yes' === $enabled);
        $user->setPlainPassword($password);
        $user->setCurrency('EUR');

        if (null !== $address) {
            $user->setShippingAddress($this->createAddress($address));
        }

        if (null !== $role) {
            $user->addRole($role);
        }

        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();

        return $user;
    }

    /**
     * @Given /^there are following orders:$/
     * @Given /^the following orders exist:$/
     * @Given /^there are orders:$/
     * @Given /^the following orders were placed:$/
     */
    public function thereAreOrders(TableNode $table)
    {
        $manager = $this->getEntityManager();
        $orderBuilder = $this->getService('sylius.builder.order');

        foreach ($table->getHash() as $data) {
            $order = $orderBuilder->create()->getOrder();

            $address = $this->createAddress($data['address']);

            $order->setShippingAddress($address);
            $order->setBillingAddress($address);

            $order->setUser($this->thereIsUser($data['user'], 'password'));

            if (isset($data['shipment'])) {
                $order->addShipment($this->createShipment($data['shipment']));
            }

            $this->getService('event_dispatcher')->dispatch('sylius.order.pre_create', new GenericEvent($order));

            $manager->persist($order);
            $manager->flush();

            $this->orders[$order->getNumber()] = $order;
        }
    }

    /**
     * @Given /^order #(\d+) has following items:$/
     */
    public function orderHasFollowingItems($number, TableNode $items)
    {
        $manager = $this->getEntityManager();
        $orderBuilder = $this->getService('sylius.builder.order');

        $orderBuilder->modify($this->orders[$number]);

        foreach ($items->getHash() as $data) {
            $product = $this->findOneByName('product', trim($data['product']));
            $quantity = $data['quantity'];

            $orderBuilder->add($product->getMasterVariant(), $product->getMasterVariant()->getPrice(), $quantity);
        }

        $manager->persist($orderBuilder->getOrder());
        $manager->flush();
    }

    /**
     * @Given /^promotion "([^""]*)" has following coupons defined:$/
     * @Given /^promotion "([^""]*)" has following coupons:$/
     */
    public function theFollowingPromotionCouponsAreDefined($name, TableNode $table)
    {
        $promotion = $this->findOneByName('promotion', $name);

        $manager = $this->getEntityManager();
        $repository = $this->getRepository('promotion_coupon');

        foreach ($table->getHash() as $data) {
            $coupon = $repository->createNew();
            $coupon->setCode($data['code']);
            $coupon->setUsageLimit($data['usage limit']);
            $coupon->setUsed($data['used']);

            $promotion->addCoupon($coupon);

            $manager->persist($coupon);
        }

        $promotion->setCouponBased(true);

        $manager->flush();
    }

    /**
     * @Given /^promotion "([^""]*)" has following rules defined:$/
     */
    public function theFollowingPromotionRulesAreDefined($name, TableNode $table)
    {
        $promotion = $this->findOneByName('promotion', $name);

        $manager = $this->getEntityManager();
        $repository = $this->getRepository('promotion_rule');

        foreach ($table->getHash() as $data) {
            $rule = $repository->createNew();
            $rule->setType(strtolower(str_replace(' ', '_', $data['type'])));
            $rule->setConfiguration($this->getConfiguration($data['configuration']));

            $promotion->addRule($rule);

            $manager->persist($rule);
        }

        $manager->flush();
    }

    /**
     * @Given /^promotion "([^""]*)" has following actions defined:$/
     */
    public function theFollowingPromotionActionsAreDefined($name, TableNode $table)
    {
        $promotion = $this->findOneByName('promotion', $name);

        $manager = $this->getEntityManager();
        $repository = $this->getRepository('promotion_action');

        foreach ($table->getHash() as $data) {
            $action = $repository->createNew();
            $action->setType(strtolower(str_replace(' ', '_', $data['type'])));
            $action->setConfiguration($this->getConfiguration($data['configuration']));

            $promotion->addAction($action);

            $manager->persist($action);
        }

        $manager->flush();
    }

    /**
     * @Given /^the following promotions exist:$/
     * @Given /^there are following promotions configured:$/
     */
    public function theFollowingPromotionsExist(TableNode $table)
    {
        $manager = $this->getEntityManager();
        $repository = $this->getRepository('promotion');

        foreach ($table->getHash() as $data) {
            $promotion = $repository->createNew();

            $promotion->setName($data['name']);
            $promotion->setDescription($data['description']);

            if (array_key_exists('usage limit', $data)) {
                $promotion->setUsageLimit($data['usage limit']);
            }
            if (array_key_exists('used', $data)) {
                $promotion->setUsed($data['used']);
            }
            if (array_key_exists('starts', $data)) {
                $promotion->setStartsAt(new \DateTime($data['starts']));
            }
            if (array_key_exists('ends', $data)) {
                $promotion->setEndsAt(new \DateTime($data['ends']));
            }

            $manager->persist($promotion);
        }

        $manager->flush();
    }

    /**
     * @Given /^there are products:$/
     * @Given /^there are following products:$/
     * @Given /^the following products exist:$/
     */
    public function thereAreProducts(TableNode $table)
    {
        $manager = $this->getEntityManager();

        foreach ($table->getHash() as $data) {
            $repository = $this->getRepository('product');

            $product = $repository->createNew();
            $product->setName(trim($data['name']));
            $product->setDescription('...');
            $product->getMasterVariant()->setPrice($data['price'] * 100);

            if (!empty($data['options'])) {
                foreach (explode(',', $data['options']) as $option) {
                    $option = $this->findOneByName('option', trim($option));
                    $product->addOption($option);
                }
            }

            if (isset($data['sku'])) {
                $product->setSku($data['sku']);
            }

            if (isset($data['variants selection']) && !empty($data['variants selection'])) {
                $product->setVariantSelectionMethod($data['variants selection']);
            }

            if (isset($data['tax category'])) {
                $product->setTaxCategory($this->findOneByName('tax_category', trim($data['tax category'])));
            }

            if (isset($data['taxons'])) {
                $taxons = new ArrayCollection();

                foreach (explode(',', $data['taxons']) as $taxonName) {
                    $taxons->add($this->findOneByName('taxon', trim($taxonName)));
                }

                $product->setTaxons($taxons);
            }

            $manager->persist($product);
        }

        $manager->flush();
    }

    /**
     * @Given /^product "([^""]*)" is available in all variations$/
     */
    public function productIsAvailableInAllVariations($productName)
    {
        $product = $this->findOneByName('product', $productName);
        $manager = $this->getEntityManager();

        $this->getService('sylius.variant_generator')->generate($product);

        foreach ($product->getVariants() as $variant) {
            $variant->setPrice($product->getMasterVariant()->getPrice());
        }

        $manager->persist($product);
        $manager->flush();
    }

    /**
     * @Given /^there is prototype "([^""]*)" with following configuration:$/
     */
    public function thereIsPrototypeWithFollowingConfiguration($name, TableNode $table)
    {
        $manager = $this->getEntityManager();
        $repository = $this->getRepository('prototype');

        $prototype = $repository->createNew();
        $prototype->setName($name);

        $data = $table->getRowsHash();

        foreach (explode(',', $data['options']) as $optionName) {
            $prototype->addOption($this->findOneByName('option', trim($optionName)));
        }

        foreach (explode(',', $data['properties']) as $propertyName) {
            $prototype->addProperty($this->findOneByName('property', trim($propertyName)));
        }

        $manager->persist($prototype);
        $manager->flush();
    }

    /**
     * @Given /^there are following options:$/
     * @Given /^the following options exist:$/
     */
    public function thereAreOptions(TableNode $table)
    {
        foreach ($table->getHash() as $data) {
            $this->thereIsOption($data['name'], $data['values'], $data['presentation']);
        }
    }

    /**
     * @Given /^I created option "([^""]*)" with values "([^""]*)"$/
     */
    public function thereIsOption($name, $values, $presentation = null)
    {
        $repository = $this->getRepository('option');
        $manager = $this->getEntityManager();

        $optionValueClass = $this->getContainer()->getParameter('sylius.model.option_value.class');
        $presentation = $presentation ?: $name;

        $option = $repository->createNew();
        $option->setName($name);
        $option->setPresentation($presentation);

        foreach (explode(',', $values) as $value) {
            $optionValue = new $optionValueClass;
            $optionValue->setValue(trim($value));

            $option->addValue($optionValue);
        }

        $manager->persist($option);
        $manager->flush();

        return $option;
    }

    /**
     * @Given /^there are following properties:$/
     * @Given /^the following properties exist:$/
     */
    public function thereAreProperties(TableNode $table)
    {
        foreach ($table->getHash() as $data) {
            $choices = isset($data['choices']) && $data['choices'] ? explode(',', $data['choices']) : array();
            $additionalData = array(
                'type'         => isset($data['type']) ? $data['type'] : 'text',
                'presentation' => isset($data['presentation']) ? $data['presentation'] : $data['name']
            );
            if ($choices) {
                $additionalData['options'] = array('choices' => $choices);
            }
            $this->thereIsProperty($data['name'], $additionalData);
        }
    }

    /**
     * @Given /^There is property "([^""]*)"$/
     * @Given /^I created property "([^""]*)"$/
     */
    public function thereIsProperty($name, $additionalData = array())
    {
        $repository = $this->getRepository('property');
        $manager = $this->getEntityManager();

        $additionalData = array_merge(array(
            'presentation' => $name,
            'type' => 'text'
        ), $additionalData);

        $property = $repository->createNew();
        $property->setName($name);
        foreach ($additionalData as $key => $value) {
            $property->{'set'.\ucfirst($key)}($value);
        }

        $manager->persist($property);
        $manager->flush();

        return $property;
    }

    /**
     * @Given /^(\w+) with following data should be created:$/
     */
    public function objectWithFollowingDataShouldBeCreated($type, TableNode $table)
    {
        $data = $table->getRowsHash();

        $object = $this->findOneByName($type, $data['name']);
        foreach ($data as $property => $value) {
            $objectValue = $object->{'get'.\ucfirst($property)}();
            if (is_array($objectValue)) {
                $objectValue = implode(',', $objectValue);;
            }
            if ($objectValue !== $value) {
                throw new \Exception(sprintf('%s object::%s has "%s" value but "%s" expected', $type, $property, $objectValue, $value));
            }
        }
    }

    /**
     * @Given /^there are following tax categories:$/
     * @Given /^the following tax categories exist:$/
     */
    public function thereAreTaxCategories(TableNode $table)
    {
        foreach ($table->getHash() as $data) {
            $this->thereIsTaxCategory($data['name']);
        }
    }

    /**
     * @Given /^There is tax category "([^""]*)"$/
     * @Given /^I created tax category "([^""]*)"$/
     */
    public function thereIsTaxCategory($name)
    {
        $repository = $this->getRepository('tax_category');
        $manager = $this->getEntityManager();

        $category = $repository->createNew();
        $category->setName($name);

        $manager->persist($category);
        $manager->flush();

        return $category;
    }

    /**
     * @Given /^there are following tax rates:$/
     * @Given /^the following tax rates exist:$/
     */
    public function thereAreTaxRates(TableNode $table)
    {
        foreach ($table->getHash() as $data) {
            $this->thereIsTaxRate($data['amount'], $data['name'], $data['category'], $data['zone']);
        }
    }

    /**
     * @Given /^there is (\d+)% tax "([^""]*)" for category "([^""]*)" within zone "([^""]*)"$/
     * @Given /^I created (\d+)% tax "([^""]*)" for category "([^""]*)" within zone "([^""]*)"$/
     */
    public function thereIsTaxRate($amount, $name, $category, $zone)
    {
        $repository = $this->getRepository('tax_rate');
        $manager = $this->getEntityManager();

        $rate = $repository->createNew();
        $rate->setName($name);
        $rate->setAmount($amount / 100);
        $rate->setCategory($this->findOneByName('tax_category', $category));
        $rate->setZone($this->findOneByName('zone', $zone));
        $rate->setCalculator('default');

        $manager->persist($rate);
        $manager->flush();

        return $rate;
    }

    /**
     * @Given /^there are following exchange rates:$/
     */
    public function thereAreExchangeRates(TableNode $table)
    {
        foreach ($table->getHash() as $data) {
            $this->thereIsExchangeRate($data['currency'], $data['rate']);
        }
    }

    /**
     * @Given /^I created exchange rate "([^""]*)"$/
     */
    public function thereIsExchangeRate($currency, $rate = 1)
    {
        $repository = $this->getRepository('exchange_rate');
        $manager = $this->getEntityManager();

        $exchangeRate = $repository->createNew();
        $exchangeRate->setCurrency($currency);
        $exchangeRate->setRate($rate);

        $manager->persist($exchangeRate);
        $manager->flush();

        return $exchangeRate;
    }

    /**
     * @Given /^the following shipping categories are configured:$/
     * @Given /^the following shipping categories exist:$/
     * @Given /^there are following shipping categories:$/
     */
    public function thereAreShippingCategories(TableNode $table)
    {
        foreach ($table->getHash() as $data) {
            $this->thereIsShippingCategory($data['name']);
        }
    }

    /**
     * @Given /^I created shipping category "([^""]*)"$/
     * @Given /^there is shipping category "([^""]*)"$/
     */
    public function thereIsShippingCategory($name)
    {
        $category = $this->getRepository('shipping_category')->createNew();
        $category->setName($name);

        $manager = $this->getEntityManager();

        $manager->persist($category);
        $manager->flush();

        return $category;
    }

    /**
     * @Given /^the following shipping methods are configured:$/
     * @Given /^the following shipping methods exist:$/
     * @Given /^there are shipping methods:$/
     */
    public function thereAreShippingMethods(TableNode $table)
    {
        foreach ($table->getHash() as $data) {
            $category = array_key_exists('category', $data) ? $data['category'] : null;
            $method = $this->thereIsShippingMethod($data['name'], $data['zone'], $category);
        }
    }

    /**
     * @Given /^I created shipping method "([^""]*)" within zone "([^""]*)"$/
     * @Given /^There is shipping method "([^""]*)" within zone "([^""]*)"$/
     */
    public function thereIsShippingMethod($name, $zoneName)
    {
        $method = $this
            ->getRepository('shipping_method')
            ->createNew()
        ;

        $method->setName($name);
        $method->setZone($this->findOneByName('zone', $zoneName));
        $method->setCalculator(DefaultCalculators::PER_ITEM_RATE);
        $method->setConfiguration(array('amount' => 25.00));

        $manager = $this->getEntityManager();

        $manager->persist($method);
        $manager->flush();

        return $method;
    }

    /**
     * @Given /^there are payment methods:$/
     * @Given /^there are following payment methods:$/
     * @Given /^the following payment methods exist:$/
     */
    public function thereArePaymentMethods(TableNode $table)
    {
        $manager = $this->getEntityManager();
        $repository = $this->getRepository('payment_method');

        foreach ($table->getHash() as $data) {
            $method = $repository->createNew();
            $method->setName(trim($data['name']));
            $method->setGateway(trim($data['gateway']));

            $enabled = true;

            if (array_key_exists('enabled', $data)) {
                $enabled = 'yes' === trim($data['enabled']);
            }

            $method->setEnabled($enabled);

            $manager->persist($method);
        }

        $manager->flush();
    }

    /**
     * @Given /^there are following countries:$/
     */
    public function thereAreCountries(TableNode $table)
    {
        foreach ($table->getHash() as $data) {
            $provinces = array_key_exists('provinces', $data) ? explode(',', $data['provinces']) : array();
            $this->thereisCountry($data['name'], $provinces);
        }
    }

    /**
     * @Given /^I created country "([^""]*)"$/
     * @Given /^there is country "([^""]*)"$/
     */
    public function thereIsCountry($name, $provinces = null)
    {
        $country = $this->getRepository('country')->createNew();

        $country->setName(trim($name));
        $country->setIsoName(array_search($name, Locale::getDisplayCountries(Locale::getDefault())));

        if (null !== $provinces) {
            $provinces = $provinces instanceof TableNode ? $provinces->getHash() : $provinces;
            foreach ($provinces as $provinceName) {
                $country->addProvince($this->thereisProvince($provinceName));
            }
        }

        $manager = $this->getEntityManager();

        $manager->persist($country);
        $manager->flush();

        return $country;
    }

    /**
     * @Given /^the following zones are defined:$/
     * @Given /^there are following zones:$/
     */
    public function thereAreFollowingZones(TableNode $table)
    {
        foreach ($table->getHash() as $data) {
            $this->thereIsZone($data['name'], $data['type'], explode(',', $data['members']));
        }
    }

    /**
     * @Given /^I created zone "([^"]*)"$/
     * @Given /^there is zone "([^"]*)"$/
     */
    public function thereIsZone($name, $type = ZoneInterface::TYPE_COUNTRY, array $members = array())
    {
        $repository = $this->getRepository('zone');

        $zone = $repository->createNew();

        $zone->setName($name);
        $zone->setType($type);

        foreach ($members as $memberName) {
            $member = $this->getService('sylius.repository.zone_member_'.$type)->createNew();
            if (ZoneInterface::TYPE_ZONE === $type) {
                $zoneable = $repository->findOneByName($memberName);
            } else {
                $zoneable = call_user_func(array($this, 'thereIs'.ucfirst($type)), $memberName);
            }

            call_user_func(array(
                $member, 'set'.ucfirst($type)),
                $zoneable
            );

            $zone->addMember($member);
        }

        $manager = $this->getEntityManager();

        $manager->persist($zone);
        $manager->flush();

        return $zone;
    }

    /**
     * @Given /^there is province "([^"]*)"$/
     */
    public function thereisProvince($name)
    {
        $province = $this->getRepository('province')->createNew();
        $province->setName($name);

        $manager = $this->getEntityManager();

        $manager->persist($province);

        return $province;
    }

    /**
     * @Given /^there are no ([^"]*)$/
     */
    public function thereAreNoResources($type)
    {
        $type = str_replace(' ', '_', StringUtil::singularify($type));
        $type = is_array($type) ? $type[1] : $type; // Hacky hack for multiple singular forms.

        $manager = $this->getEntityManager();

        foreach ($this->getRepository($type)->findAll() as $resource) {
            $manager->remove($resource);
        }

        $manager->flush();
    }

    /**
     * Create an address instance from string.
     *
     * @param string $string
     *
     * @return AddressInterface
     */
    private function createAddress($string)
    {
        $address = $this->getRepository('address')->createNew();

        $addressData = explode(',', $string);
        $addressData = array_map('trim', $addressData);

        list($firstname, $lastname) = explode(' ', $addressData[0]);

        $address->setFirstname(trim($firstname));
        $address->setLastname(trim($lastname));
        $address->setStreet($addressData[1]);
        $address->setCity($addressData[2]);
        $address->setPostcode($addressData[3]);
        $address->setCountry($this->findOneByName('country', $addressData[4]));

        return $address;
    }

    /**
     * Create an shipment instance from string.
     *
     * @param string $string
     *
     * @return ShipmentInterface
     */
    private function createShipment($string)
    {
        $shipment = $this->getRepository('shipment')->createNew();

        $shipmentData = explode(',', $string);
        $shipmentData = array_map('trim', $shipmentData);

        $shipment->setMethod($this->getRepository('shipping_method')->findOneByName($shipmentData[0]));

        return $shipment;
    }

    /**
     * Configuration converter.
     *
     * @param string $configuraitonString
     *
     * @return array
     */
    private function getConfiguration($configuraitonString)
    {
        $configuration = array();
        $list = explode(',', $configuraitonString);

        foreach ($list as $parameter) {
            list($key, $value) = explode(':', $parameter);
            $configuration[strtolower(trim(str_replace(' ', '_', $key)))] = trim($value);
        }

        return $configuration;
    }

    /**
     * Find one resource by name.
     *
     * @param string $
     * @param string $name
     *
     * @return object
     */
    public function findOneByName($type, $name)
    {
        return $this->findOneBy($type, array('name' => trim($name)));
    }

    /**
     * Find one resource by criteria.
     *
     * @param string $type
     * @param array  $criteria
     *
     * @return object
     */
    public function findOneBy($type, array $criteria)
    {
        $resource = $this
            ->getRepository($type)
            ->findOneBy($criteria)
        ;

        if (null === $resource) {
            throw new \InvalidArgumentException(
                sprintf('%s for criteria "%s" was not found.', str_replace('_', ' ', ucfirst($type)), serialize($criteria))
            );
        }

        return $resource;
    }

    /**
     * Get repository by resource name.
     *
     * @param string $resource
     *
     * @return ObjectRepository
     */
    public function getRepository($resource)
    {
        return $this->getService('sylius.repository.'.$resource);
    }

    /**
     * Get entity manager.
     *
     * @return EntityManager
     */
    public function getEntityManager()
    {
        return $this->getContainer()->get('doctrine')->getManager();
    }

    /**
     * Returns Container instance.
     *
     * @return ContainerInterface
     */
    protected function getContainer()
    {
        return $this->kernel->getContainer();
    }

    /**
     * Get service by id.
     *
     * @param string $id
     *
     * @return object
     */
    protected function getService($id)
    {
        return $this->getContainer()->get($id);
    }
}
