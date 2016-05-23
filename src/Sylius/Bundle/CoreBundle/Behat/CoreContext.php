<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Behat;

use Behat\Gherkin\Node\TableNode;
use Behat\Mink\Driver\Selenium2Driver;
use Sylius\Bundle\ResourceBundle\Behat\DefaultContext;
use Sylius\Component\Addressing\Model\AddressInterface;
use Sylius\Component\Cart\SyliusCartEvents;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Core\Model\ShippingMethodInterface;
use Sylius\Component\Core\Model\TaxRateInterface;
use Sylius\Component\Core\Model\UserInterface;
use Sylius\Component\Core\Pricing\Calculators as PriceCalculators;
use Sylius\Component\Currency\Model\CurrencyInterface;
use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Component\Order\OrderTransitions;
use Sylius\Component\Payment\Model\PaymentMethodInterface;
use Sylius\Component\Rbac\Model\RoleInterface;
use Sylius\Component\Shipping\Calculator\DefaultCalculators;
use Sylius\Component\Shipping\ShipmentTransitions;
use Sylius\Component\Taxation\Model\TaxCategoryInterface;
use Sylius\Component\User\Model\GroupableInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class CoreContext extends DefaultContext
{
    /**
     * @var OrderInterface[]
     */
    protected $orders = [];

    /**
     * @Given store has default configuration
     */
    public function storeHasDefaultConfiguration()
    {
        $manager = $this->getEntityManager();

        /** @var CurrencyInterface $currency */
        $currency = $this->getFactory('currency')->createNew();
        $currency->setCode('EUR');
        $currency->setExchangeRate(1);
        $manager->persist($currency);

        /** @var LocaleInterface $locale */
        $locale = $this->getFactory('locale')->createNew();
        $locale->setCode('en_US');
        $manager->persist($locale);

        /* @var ChannelInterface $channel */
        $channel = $this->getFactory('channel')->createNew();
        $channel->setCode('DEFAULT-WEB');
        $channel->setName('Default');
        $channel->setHostname('http://example.com');
        $channel->addCurrency($currency);
        $channel->setDefaultCurrency($currency);
        $channel->addLocale($locale);
        $channel->setDefaultLocale($locale);
        $manager->persist($channel);

        $manager->flush();
    }

    /**
     * @Given I am logged in as :role
     */
    public function iAmLoggedInAsAuthorizationRole($role)
    {
        $this->iAmLoggedInAsRole('ROLE_ADMINISTRATION_ACCESS', 'sylius@example.com', [$role]);
    }

    /**
     * @Given /^I am logged in user$/
     * @Given /^I am logged in as user "([^""]*)"$/
     */
    public function iAmLoggedInUser($email = 'sylius@example.com')
    {
        $this->iAmLoggedInAsRole('ROLE_USER', $email);
    }

    /**
     * @Given /^I am not logged in$/
     */
    public function iAmNotLoggedIn()
    {
        $this->getSession()->restart();
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
        $finite = $this->getService('sm.factory');
        $orderFactory = $this->getFactory('order');
        $shipmentProcessor = $this->getService('sylius.processor.shipment_processor');

        /** @var $paymentMethod PaymentMethodInterface */
        $paymentMethod = $this->getFactory('payment_method')->createNew();
        $paymentMethod->setName('Stripe');
        $paymentMethod->setGateway('stripe');
        $paymentMethod->setCode('PM100');
        $manager->persist($paymentMethod);

        $currentOrderNumber = 1;
        foreach ($table->getHash() as $data) {
            $address = $this->createAddress($data['address']);

            /* @var $order OrderInterface */
            $order = $orderFactory->createNew();
            $order->setShippingAddress($address);
            $order->setBillingAddress($address);

            $customer = $this->thereIsCustomer($data['customer']);
            $customer->addAddress($address);
            $order->setCustomer($customer);

            if (isset($data['shipment']) && '' !== trim($data['shipment'])) {
                $order->addShipment($this->createShipment($data['shipment']));
            }

            $order->setNumber(str_pad($currentOrderNumber, 9, 0, STR_PAD_LEFT));

            $finite->get($order, OrderTransitions::GRAPH)->apply(OrderTransitions::SYLIUS_CREATE);

            $this->createPayment($order, $paymentMethod);

            $currency = isset($data['currency']) ? trim($data['currency']) : 'EUR';
            $order->setCurrency($currency);

            if (isset($data['exchange_rate']) && '' !== trim($data['exchange_rate'])) {
                $order->setExchangeRate($data['exchange_rate']);
            }

            $order->setPaymentState(PaymentInterface::STATE_COMPLETED);

            $order->complete();

            $shipmentProcessor->updateShipmentStates($order->getShipments(), ShipmentTransitions::SYLIUS_PREPARE);

            $manager->persist($order);

            $this->orders[$order->getNumber()] = $order;

            ++$currentOrderNumber;
        }

        $manager->flush();
    }

    /**
     * @Given /^order #(\d+) has following items:$/
     */
    public function orderHasFollowingItems($number, TableNode $items)
    {
        $manager = $this->getEntityManager();
        $orderItemFactory = $this->getFactory('order_item');
        $orderItemQuantityModifier = $this->getService('sylius.order_item_quantity_modifier');

        $order = $this->orders[$number];

        foreach ($items->getHash() as $data) {
            $product = $this->findOneByName('product', trim($data['product']));

            /* @var $item OrderItemInterface */
            $item = $orderItemFactory->createNew();
            $item->setVariant($product->getFirstVariant());
            $item->setUnitPrice($product->getPrice());

            $orderItemQuantityModifier->modify($item, $data['quantity']);

            $order->addItem($item);
        }

        $order->complete();

        $this->getService('sylius.order_processing.payment_processor')->processOrderPayments($order);
        $this->getService('event_dispatcher')->dispatch(SyliusCartEvents::CART_CHANGE, new GenericEvent($order));

        $order->setPaymentState(PaymentInterface::STATE_COMPLETED);

        $manager->persist($order);
        $manager->flush();
    }

    /**
     * @Given /^there are following users:$/
     */
    public function thereAreFollowingUsers(TableNode $table)
    {
        foreach ($table->getHash() as $data) {
            $this->thereIsUser(
                $data['email'],
                isset($data['password']) ? $data['password'] : $this->faker->word(),
                'ROLE_USER',
                isset($data['enabled']) ? $data['enabled'] : true,
                isset($data['address']) && !empty($data['address']) ? $data['address'] : null,
                isset($data['groups']) && !empty($data['groups']) ? explode(',', $data['groups']) : [],
                false,
                [],
                isset($data['created at']) ? new \DateTime($data['created at']) : null
            );
        }

        $this->getEntityManager()->flush();
    }

    /**
     * @Given /^there are following customers:$/
     * @Given /^the following customers exist:$/
     */
    public function thereAreFollowingCustomers(TableNode $table)
    {
        foreach ($table->getHash() as $data) {
            $this->thereIsCustomer(
                $data['email'],
                isset($data['address']) && !empty($data['address']) ? $data['address'] : null,
                isset($data['groups']) && !empty($data['groups']) ? explode(',', $data['groups']) : [],
                false,
                isset($data['created at']) ? new \DateTime($data['created at']) : null
            );
        }

        $this->getEntityManager()->flush();
    }

    /**
     * @Given /^there are groups:$/
     * @Given /^there are following groups:$/
     * @Given /^the following groups exist:$/
     */
    public function thereAreGroups(TableNode $table)
    {
        $manager = $this->getEntityManager();
        $factory = $this->getFactory('group');

        foreach ($table->getHash() as $data) {
            $group = $factory->createNew();
            $group->setName(trim($data['name']));

            $manager->persist($group);
        }

        $manager->flush();
    }

    /**
     * @Given /^the following addresses exist:$/
     */
    public function theFollowingAddressesExist(TableNode $table)
    {
        $manager = $this->getEntityManager();

        foreach ($table->getHash() as $data) {
            $address = $this->createAddress($data['address']);

            $user = $this->thereIsUser($data['user'], 'sylius', 'ROLE_USER', 'yes', null, []);
            $user->getCustomer()->addAddress($address);

            $manager->persist($address);
            $manager->persist($user);
        }

        $manager->flush();
    }

    public function thereIsUser($email, $password, $role = null, $enabled = 'yes', $address = null, $groups = [], $flush = true, array $authorizationRoles = [], $createdAt = null)
    {
        if (null !== $user = $this->getRepository('user')->findOneByEmail($email)) {
            return $user;
        }

        /* @var $user UserInterface */
        $user = $this->createUser($email, $password, $role, $enabled, $address, $groups, $authorizationRoles, $createdAt);

        $this->getEntityManager()->persist($user);
        if ($flush) {
            $this->getEntityManager()->flush();
        }

        return $user;
    }

    protected function thereIsCustomer($email, $address = null, $groups = [], $flush = true, $createdAt = null)
    {
        if (null !== $customer = $this->getRepository('customer')->findOneByEmail($email)) {
            return $customer;
        }

        /* @var $customer CustomerInterface */
        $customer = $this->createCustomer($email, $address, $groups, $createdAt);

        $this->getEntityManager()->persist($customer);
        if ($flush) {
            $this->getEntityManager()->flush();
        }

        return $customer;
    }

    /**
     * @Given /^product "([^""]*)" has the following volume based pricing:$/
     */
    public function productHasTheFollowingVolumeBasedPricing($productName, TableNode $table)
    {
        /* @var $product ProductInterface */
        $product = $this->findOneByName('product', $productName);
        $variant = $product->getFirstVariant();

        /* @var $variant ProductVariantInterface */
        $variant->setPricingCalculator(PriceCalculators::VOLUME_BASED);
        $configuration = [];

        foreach ($table->getHash() as $data) {
            if (false !== strpos($data['range'], '+')) {
                $min = (int) trim(str_replace('+', '', $data['range']));
                $max = null;
            } else {
                list($min, $max) = array_map(function ($value) { return (int) trim($value); }, explode('-', $data['range']));
            }

            $configuration[] = [
                'min' => $min,
                'max' => $max,
                'price' => (int) ($data['price'] * 100),
            ];
        }

        $variant->setPricingConfiguration($configuration);

        $manager = $this->getEntityManager();
        $manager->persist($product);
        $manager->flush();
    }

    /**
     * @Given /^product "([^""]*)" has the following group based pricing:$/
     */
    public function productHasTheFollowingGroupBasedPricing($productName, TableNode $table)
    {
        $product = $this->findOneByName('product', $productName);
        $variant = $product->getFirstVariant();

        /* @var $variant ProductVariantInterface */
        $variant->setPricingCalculator(PriceCalculators::GROUP_BASED);
        $configuration = [];

        foreach ($table->getHash() as $data) {
            $group = $this->findOneByName('group', trim($data['group']));
            $configuration[$group->getId()] = (float) $data['price'] * 100;
        }

        $variant->setPricingConfiguration($configuration);

        $manager = $this->getEntityManager();
        $manager->persist($product);
        $manager->flush();
    }

    /**
     * @Given /^there are following tax rates:$/
     * @Given /^the following tax rates exist:$/
     */
    public function thereAreTaxRates(TableNode $table)
    {
        foreach ($table->getHash() as $data) {
            $this->thereIsTaxRate($data['amount'], $data['name'], $data['code'], $data['category'], $data['zone'], isset($data['included in price?']) ? $data['included in price?'] : false, false);
        }

        $this->getEntityManager()->flush();
    }

    /**
     * @Given /^there is (\d+)% tax "([^""]*)" with code "([^""]*)" for category "([^""]*)" with zone "([^""]*)"$/
     * @Given /^I created (\d+)% tax "([^""]*)" with code "([^""]*)" for category "([^""]*)" with zone "([^""]*)"$/
     */
    public function thereIsTaxRate($amount, $name, $code, $category, $zone, $includedInPrice = false, $flush = true)
    {
        /* @var $rate TaxRateInterface */
        $rate = $this->getFactory('tax_rate')->createNew();
        $rate->setName($name);
        $rate->setAmount($amount / 100);
        $rate->setIncludedInPrice($includedInPrice);
        $rate->setCategory($this->findOneByName('tax_category', $category));
        $rate->setZone($this->findOneByName('zone', $zone));
        $rate->setCalculator('default');
        $rate->setCode($code);

        $manager = $this->getEntityManager();
        $manager->persist($rate);
        if ($flush) {
            $manager->flush();
        }

        return $rate;
    }

    /**
     * @Given /^the following shipping methods are configured:$/
     * @Given /^the following shipping methods exist:$/
     * @Given /^there are shipping methods:$/
     */
    public function thereAreShippingMethods(TableNode $table)
    {
        foreach ($table->getHash() as $data) {
            $calculator = array_key_exists('calculator', $data) ? str_replace(' ', '_', strtolower($data['calculator'])) : DefaultCalculators::PER_UNIT_RATE;
            $configuration = array_key_exists('configuration', $data) ? $this->getConfiguration($data['configuration']) : null;
            $taxCategory = (isset($data['tax category'])) ? $this->findOneByName('tax_category', trim($data['tax category'])) : null;

            if (!isset($data['enabled'])) {
                $data['enabled'] = 'yes';
            }

            $this->thereIsShippingMethod($data['name'], $data['code'], $data['zone'], $calculator, $taxCategory, $configuration, 'yes' === $data['enabled'], false);
        }

        $this->getEntityManager()->flush();
    }

    /**
     * @Given /^I created shipping method "([^""]*)" with code "([^""]*)" and zone "([^""]*)"$/
     * @Given /^There is shipping method "([^""]*)" with code "([^""]*)" and zone "([^""]*)"$/
     * @Given /^there is an enabled shipping method "([^""]*)" with code "([^""]*)" and zone "([^""]*)"$/
     */
    public function thereIsShippingMethod($name, $code, $zoneName, $calculator = DefaultCalculators::PER_UNIT_RATE, TaxCategoryInterface $taxCategory = null, array $configuration = null, $enabled = true, $flush = true)
    {
        $repository = $this->getRepository('shipping_method');
        $factory = $this->getFactory('shipping_method');

        /* @var $method ShippingMethodInterface */
        if (null === $method = $repository->findOneByName($name)) {
            $method = $factory->createNew();
            $method->setName($name);
            $method->setCode($code);
            $method->setZone($this->findOneByName('zone', $zoneName));
            $method->setCalculator($calculator);
            $method->setTaxCategory($taxCategory);
            $method->setConfiguration($configuration ?: ['amount' => 2500]);
        };

        $method->setEnabled($enabled);

        $manager = $this->getEntityManager();
        $manager->persist($method);
        if ($flush) {
            $manager->flush();
        }

        return $method;
    }

    /**
     * @Given /^there is a disabled shipping method "([^""]*)" with code "([^""]*)" and zone "([^""]*)"$/
     */
    public function thereIsDisabledShippingMethod($name, $code, $zoneName)
    {
        $this->thereIsShippingMethod($name, $code, $zoneName, DefaultCalculators::PER_UNIT_RATE, null, null, false);
    }

    /**
     * @Given /^the following locales are defined:$/
     * @Given /^there are following locales configured:$/
     */
    public function thereAreLocales(TableNode $table)
    {
        $repository = $this->getRepository('locale');
        $manager = $this->getEntityManager();
        $factory = $this->getFactory('locale');

        $locales = $repository->findAll();
        foreach ($locales as $locale) {
            $manager->remove($locale);
        }

        $manager->flush();
        $manager->clear();

        foreach ($table->getHash() as $data) {
            $locale = $factory->createNew();

            if (isset($data['code'])) {
                $locale->setCode($data['code']);
            } elseif (isset($data['name'])) {
                $locale->setCode($this->getLocaleCodeByEnglishLocaleName($data['name']));
            } else {
                throw new \InvalidArgumentException('Locale definition should have either code or name');
            }

            if (isset($data['enabled'])) {
                $locale->setEnabled('yes' === $data['enabled']);
            }

            $manager->persist($locale);
        }

        $manager->flush();
    }

    /**
     * @Given /^there are following locales configured and assigned to the default channel:$/
     */
    public function thereAreLocalesAssignedToDefaultChannel(TableNode $table)
    {
        $this->thereAreLocales($table);

        /** @var ChannelInterface $defaultChannel */
        $defaultChannel = $this->getRepository('channel')->findOneBy(['code' => 'DEFAULT-WEB']);

        /** @var LocaleInterface[] $locales */
        $locales = $this->getRepository('locale')->findAll();
        foreach ($locales as $locale) {
            $defaultChannel->addLocale($locale);
        }

        $this->getEntityManager()->flush();
    }

    /**
     * @Given /^product "([^""]*)" is available in all variations$/
     */
    public function productIsAvailableInAllVariations($productName)
    {
        /** @var ProductInterface $product */
        $product = $this->findOneByName('product', $productName);

        $this->generateProductVariations($product);

        $this->getEntityManager()->flush();
    }

    /**
     * @Given /^product "([^""]*)" has no variants$/
     */
    public function productHasNoVariants($productName)
    {
        /** @var ProductInterface $product */
        $product = $this->findOneByName('product', $productName);
        $product->getVariants()->clear();

        $this->getEntityManager()->flush();
    }

    /**
     * @Given all products are available in all variations
     */
    public function allProductsAreAvailableInAllVariations()
    {
        /** @var ProductInterface[] $products */
        $products = $this->getRepository('product')->findAll();
        foreach ($products as $product) {
            if ($product->hasOptions()) {
                $this->generateProductVariations($product);
            }
        }

        $this->getEntityManager()->flush();
    }

    /**
     * @Given /^user "([^"]*)" has been deleted$/
     */
    public function userHasBeenDeleted($customerEmail)
    {
        $userRepository = $this->getRepository('user');

        $user = $userRepository->findOneByEmail($customerEmail);

        $userRepository->remove($user);
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
        $addressData = $this->processAddress($string);

        list($firstname, $lastname) = explode(' ', $addressData[0]);

        /* @var $address AddressInterface */
        $address = $this->getFactory('address')->createNew();
        $address->setFirstname(trim($firstname));
        $address->setLastname(trim($lastname));
        $address->setStreet($addressData[1]);
        $address->setPostcode($addressData[2]);
        $address->setCity($addressData[3]);
        $address->setCountryCode($this->getCountryCodeByEnglishCountryName($addressData[4]));

        return $address;
    }

    /**
     * @param string $address
     *
     * @return array
     */
    protected function processAddress($address)
    {
        $addressData = explode(',', $address);
        $addressData = array_map('trim', $addressData);

        return $addressData;
    }

    /**
     * Create an payment instance.
     *
     * @param OrderInterface         $order
     * @param PaymentMethodInterface $method
     */
    private function createPayment(OrderInterface $order, PaymentMethodInterface $method)
    {
        /** @var $payment PaymentInterface */
        $payment = $this->getFactory('payment')->createNew();
        $payment->setOrder($order);
        $payment->setMethod($method);
        $payment->setAmount($order->getTotal());
        $payment->setCurrency($order->getCurrency() ?: 'EUR');
        $payment->setState(PaymentInterface::STATE_COMPLETED);

        $order->addPayment($payment);
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
        $shipmentData = explode(',', $string);
        $shipmentData = array_map('trim', $shipmentData);

        /* @var $shippingMethod ShippingMethodInterface */
        $shippingMethod = $this->getRepository('shipping_method')->findOneByName($shipmentData[0]);

        /* @var $shipment ShipmentInterface */
        $shipment = $this->getFactory('shipment')->createNew();
        $shipment->setMethod($shippingMethod);
        if (isset($shipmentData[1])) {
            $shipment->setState($shipmentData[1]);
        }
        if (isset($shipmentData[2])) {
            $shipment->setTracking($shipmentData[2]);
        }

        return $shipment;
    }

    /**
     * Create user and login with given role.
     *
     * @param string $role
     * @param string $email
     * @param array  $authorizationRoles
     */
    private function iAmLoggedInAsRole($role, $email = 'sylius@example.com', array $authorizationRoles = [])
    {
        $user = $this->thereIsUser($email, 'sylius', $role, 'yes', null, [], true, $authorizationRoles);

        $token = new UsernamePasswordToken($user, $user->getPassword(), 'administration', $user->getRoles());

        $session = $this->getService('session');
        $session->set('_security_user', serialize($token));
        $session->save();

        $this->prepareSessionIfNeeded();

        $this->getSession()->setCookie($session->getName(), $session->getId());
        $this->getService('security.token_storage')->setToken($token);
    }

    /**
     * @param GroupableInterface $groupableObject
     * @param array              $groups
     */
    protected function assignGroups(GroupableInterface $groupableObject, array $groups)
    {
        foreach ($groups as $groupName) {
            if ($group = $this->findOneByName('group', $groupName)) {
                $groupableObject->addGroup($group);
            }
        }
    }

    /**
     * @param array         $authorizationRoles
     * @param UserInterface $user
     */
    protected function assignAuthorizationRoles(UserInterface $user, array $authorizationRoles = [])
    {
        foreach ($authorizationRoles as $role) {
            try {
                $authorizationRole = $this->findOneByName('role', $role);
            } catch (\InvalidArgumentException $exception) {
                $authorizationRole = $this->createAuthorizationRole($role);
                $this->getEntityManager()->persist($authorizationRole);
            }

            $user->addAuthorizationRole($authorizationRole);
        }
    }

    /**
     * @param $email
     * @param $address
     * @param $groups
     * @param $createdAt
     *
     * @return CustomerInterface
     */
    protected function createCustomer($email, $address = null, $groups = [], $createdAt = null)
    {
        $addressData = $this->processAddress($address);

        $customer = $this->getFactory('customer')->createNew();
        $customer->setFirstname(null === $address ? $this->faker->firstName : $addressData[0]);
        $customer->setLastname(null === $address ? $this->faker->lastName : $addressData[1]);
        $customer->setEmail($email);
        $customer->setEmailCanonical($email);
        $customer->setCreatedAt(null === $createdAt ? new \DateTime() : $createdAt);
        if (null !== $address) {
            $customer->setShippingAddress($this->createAddress($address));
        }
        $this->assignGroups($customer, $groups);

        return $customer;
    }

    /**
     * @param $email
     * @param $password
     * @param $role
     * @param $enabled
     * @param $address
     * @param $groups
     * @param array $authorizationRoles
     * @param $createdAt
     *
     * @return UserInterface
     */
    protected function createUser($email, $password, $role = null, $enabled = 'yes', $address = null, array $groups = [], array $authorizationRoles = [], $createdAt = null)
    {
        $user = $this->getFactory('user')->createNew();
        $customer = $this->createCustomer($email, $address, $groups, $createdAt);
        $user->setCustomer($customer);
        $user->setUsername($email);
        $user->setEmail($email);
        $user->setEnabled('yes' === $enabled);
        $user->setCreatedAt(null === $createdAt ? new \DateTime() : $createdAt);
        $user->setPlainPassword($password);
        $user->setUsernameCanonical($email);
        $user->setEmailCanonical($email);
        $this->getService('sylius.user.password_updater')->updatePassword($user);

        if (null !== $role) {
            $user->addRole($role);
        }
        $this->assignAuthorizationRoles($user, $authorizationRoles);

        return $user;
    }

    /**
     * @param string $role
     *
     * @return RoleInterface
     */
    protected function createAuthorizationRole($role)
    {
        $authorizationRole = $this->getFactory('role')->createNew();
        $authorizationRole->setCode($role);
        $authorizationRole->setName(ucfirst($role));
        $authorizationRole->setSecurityRoles(['ROLE_ADMINISTRATION_ACCESS']);

        return $authorizationRole;
    }

    /**
     * @param ProductInterface $product
     */
    private function generateProductVariations($product)
    {
        $this->getService('sylius.generator.variant')->generate($product);

        foreach ($product->getVariants() as $variant) {
            $variant->setPrice((null !== $product->getPrice()) ? $product->getPrice() : rand(1000, 10000));
            $variant->setCode($this->faker->unique->uuid);
        }

        $this->getEntityManager()->persist($product);
    }

    private function prepareSessionIfNeeded()
    {
        if (!$this->getSession()->getDriver() instanceof Selenium2Driver) {
            return;
        }

        if (false !== strpos($this->getSession()->getCurrentUrl(), $this->getMinkParameter('base_url'))) {
            return;
        }

        $this->visitPath('/');
    }
}
