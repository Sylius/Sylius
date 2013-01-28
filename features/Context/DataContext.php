<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Context;

use Behat\Behat\Context\BehatContext;
use Behat\Gherkin\Node\TableNode;
use Behat\Symfony2Extension\Context\KernelAwareInterface;
use Sylius\Bundle\AddressingBundle\Model\ZoneInterface;
use Sylius\Bundle\ShippingBundle\Calculator\DefaultCalculators;
use Symfony\Component\Form\Util\FormUtil;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Locale\Locale;

/**
 * Data writing and reading context.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class DataContext extends BehatContext implements KernelAwareInterface
{
    /**
     * Repository services map.
     *
     * @var array
     */
    public $repositories = array(
        'tax_category'      => 'sylius_taxation.repository.category',
        'tax_rate'          => 'sylius_taxation.repository.rate',
        'shipping_category' => 'sylius_shipping.repository.category',
        'shipping_method'   => 'sylius_shipping.repository.method',
        'country'           => 'sylius_addressing.repository.country',
        'province'          => 'sylius_addressing.repository.province',
        'zone'              => 'sylius_addressing.repository.zone',
    );

    /**
     * {@inheritdoc}
     */
    public function setKernel(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    /**
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
            $product->getMasterVariant()->setPrice($data['price']);

            if (!empty($data['options'])) {
                foreach (explode(',', $data['options']) as $option) {
                    $option = $this->findOneByName('option', trim($option));
                    $product->addOption($option);
                }
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
            $this->thereIsProperty($data['name'], $data['presentation']);
        }
    }

    /**
     * @Given /^There is property "([^""]*)"$/
     * @Given /^I created property "([^""]*)"$/
     */
    public function thereIsProperty($name, $presentation = null)
    {
        $repository = $this->getRepository('property');
        $manager = $this->getEntityManager();

        $presentation = $presentation ?: $name;

        $property = $repository->createNew();
        $property->setName($name);
        $property->setPresentation($presentation);

        $manager->persist($property);
        $manager->flush();

        return $property;
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
        $repository = $this->getService('sylius_taxation.repository.rate');
        $manager = $this->getService('sylius_taxation.manager.rate');

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
            $method = $this->thereIsShippingMethod($data['name'], $data['zone'], $data['category']);
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
     * @Given /^there are following countries:$/
     */
    public function thereAreCountries(TableNode $table)
    {
        foreach ($table->getHash() as $data) {
            $this->thereisCountry($data['name'], explode(',', $data['provinces']));
        }
    }

    /**
     * @Given /^I created country "([^""]*)"$/
     * @Given /^there is country "([^""]*)"$/
     */
    public function thereIsCountry($name, $provinces = null)
    {
        $country = $this->getRepository('country')->createNew();

        $country->setName($name);
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
            $member = $this->getService('sylius_addressing.repository.zone_member_'.$type)->createNew();
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
        $type = str_replace(' ', '_', FormUtil::singularify($type));
        $type = is_array($type) ? $type[1] : $type; // Hacky hack for multiple singular forms.

        $manager = $this->getEntityManager();

        foreach ($this->getRepository($type)->findAll() as $resource) {
            $manager->remove($resource);
        }

        $manager->flush();
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
        $resource = $this
            ->getRepository($type)
            ->findOneBy(array('name' => $name))
        ;

        if (null === $resource) {
            throw new \InvalidArgumentException(
                sprintf('%s with name "%s" was not found.', str_replace('_', ' ', ucfirst($type)), $name)
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
        if (!isset($this->repositories[$resource])) {
            return $this->getService('sylius.repository.'.$resource);
        }

        return $this->getService($this->repositories[$resource]);
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
