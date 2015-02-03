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
use Sylius\Bundle\ResourceBundle\Behat\DefaultContext;
use Sylius\Component\Addressing\Model\AddressInterface;
use Sylius\Component\Cart\SyliusCartEvents;
use Sylius\Component\Channel\Model\ChannelsAwareInterface;
use Sylius\Component\Core\Model\ChannelInterface;
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
use Sylius\Component\Order\OrderTransitions;
use Sylius\Component\Payment\Model\PaymentMethodInterface;
use Sylius\Component\Shipping\Calculator\DefaultCalculators;
use Sylius\Component\Shipping\ShipmentTransitions;
use Symfony\Component\EventDispatcher\GenericEvent;

class CoreContext extends DefaultContext
{
    /**
     * Created orders.
     *
     * @var OrderInterface[]
     */
    protected $orders = array();

    /**
     * @Given I am logged in as :role
     */
    public function iAmLoggedInAsAuthorizationRole($role)
    {
        $this->iAmLoggedInAsRole('ROLE_ADMINISTRATION_ACCESS', 'sylius@example.com', array($role));
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
        $this->getSession()->visit($this->generatePageUrl('fos_user_security_logout'));
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
        $finite  = $this->getService('sm.factory');
        $orderRepository   = $this->getRepository('order');
        $shipmentProcessor = $this->getService('sylius.processor.shipment_processor');

        /** @var $paymentMethod PaymentMethodInterface */
        $paymentMethod = $this->getRepository('payment_method')->createNew();
        $paymentMethod->setName('Stripe');
        $paymentMethod->setGateway('stripe');
        $manager->persist($paymentMethod);

        $currentOrderNumber = 1;
        foreach ($table->getHash() as $data) {
            $address = $this->createAddress($data['address']);

            /* @var $order OrderInterface */
            $order = $orderRepository->createNew();
            $order->setShippingAddress($address);
            $order->setBillingAddress($address);

            $order->setUser($this->thereIsUser($data['user'], 'sylius'));

            if (isset($data['shipment']) && '' !== trim($data['shipment'])) {
                $order->addShipment($this->createShipment($data['shipment']));
            }

            $order->setNumber(str_pad($currentOrderNumber, 9, 0, STR_PAD_LEFT));

            $finite->get($order, OrderTransitions::GRAPH)->apply(OrderTransitions::SYLIUS_CREATE);

            $this->createPayment($order, $paymentMethod);

            $order->setCurrency('EUR');
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
        $orderItemRepository = $this->getRepository('order_item');

        $order = $this->orders[$number];

        foreach ($items->getHash() as $data) {
            $product = $this->findOneByName('product', trim($data['product']));

            /* @var $item OrderItemInterface */
            $item = $orderItemRepository->createNew();
            $item->setVariant($product->getMasterVariant());
            $item->setUnitPrice($product->getMasterVariant()->getPrice());
            $item->setQuantity($data['quantity']);

            $order->addItem($item);
        }


        $order->calculateTotal();
        $order->complete();

        $this->getService('sylius.order_processing.payment_processor')->createPayment($order);
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
                isset($data['groups']) && !empty($data['groups']) ? explode(',', $data['groups']) : array(),
                false,
                array(),
                isset($data['created at']) ? new \DateTime($data["created at"]) : null
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
        $repository = $this->getRepository('group');

        foreach ($table->getHash() as $data) {
            $group = $repository->createNew();
            $group->setName(trim($data['name']));

            $roles = explode(',', $data['roles']);
            $roles = array_map('trim', $roles);

            $group->setRoles($roles);

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
            $user = $this->thereIsUser($data['user'], 'sylius', null, 'yes', null, array(), false);
            $user->addAddress($address);
            $manager->persist($address);
            $manager->persist($user);
        }

        $manager->flush();
    }

    public function thereIsUser($email, $password, $role = null, $enabled = 'yes', $address = null, $groups = array(), $flush = true, array $authorizationRoles = array(), $createdAt = null)
    {
        if (null === $user = $this->getRepository('user')->findOneBy(array('email' => $email))) {
            $addressData = explode(',', $address);
            $addressData = array_map('trim', $addressData);

            /* @var $user UserInterface */
            $user = $this->getRepository('user')->createNew();
            $user->setFirstname(null === $address ? $this->faker->firstName : $addressData[0]);
            $user->setLastname(null === $address ? $this->faker->lastName : $addressData[1]);
            $user->setEmail($email);
            $user->setEnabled('yes' === $enabled);
            $user->setCreatedAt(null === $createdAt ? new \DateTime() : $createdAt);
            $user->setPlainPassword($password);

            if (null !== $address) {
                $user->setShippingAddress($this->createAddress($address));
            }

            if (null !== $role) {
                $user->addRole($role);
            }

            $this->getEntityManager()->persist($user);

            foreach ($groups as $groupName) {
                if ($group = $this->findOneByName('group', $groupName)) {
                    $user->addGroup($group);
                }
            }

            foreach ($authorizationRoles as $role) {
                try {
                    $authorizationRole = $this->findOneByName('role', $role);
                } catch (\InvalidArgumentException $exception) {
                    $authorizationRole = $this->getService('sylius.repository.role')->createNew();

                    $authorizationRole->setCode($role);
                    $authorizationRole->setName(ucfirst($role));
                    $authorizationRole->setSecurityRoles(array('ROLE_ADMINISTRATION_ACCESS'));

                    $this->getEntityManager()->persist($authorizationRole);
                }

                $user->addAuthorizationRole($authorizationRole);
            }

            if ($flush) {
                $this->getEntityManager()->flush();
            }
        }

        return $user;
    }

    /**
     * @Given /^product "([^""]*)" has the following volume based pricing:$/
     */
    public function productHasTheFollowingVolumeBasedPricing($productName, TableNode $table)
    {
        /* @var $product ProductInterface */
        $product = $this->findOneByName('product', $productName);
        $masterVariant = $product->getMasterVariant();

        /* @var $masterVariant ProductVariantInterface */
        $masterVariant->setPricingCalculator(PriceCalculators::VOLUME_BASED);
        $configuration = array();

        foreach ($table->getHash() as $data) {
            if (false !== strpos($data['range'], '+')) {
                $min = (int) trim(str_replace('+', '', $data['range']));
                $max = null;
            } else {
                list($min, $max) = array_map(function ($value) { return (int) trim($value); }, explode('-', $data['range']));
            }

            $configuration[] = array(
                'min'   => $min,
                'max'   => $max,
                'price' => (int) ($data['price'] * 100)
            );
        }

        $masterVariant->setPricingConfiguration($configuration);

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
        $masterVariant = $product->getMasterVariant();

        /* @var $masterVariant ProductVariantInterface */
        $masterVariant->setPricingCalculator(PriceCalculators::GROUP_BASED);
        $configuration = array();

        foreach ($table->getHash() as $data) {
            $group = $this->findOneByName('group', trim($data['group']));
            $configuration[$group->getId()] = (float) $data['price'] * 100;
        }

        $masterVariant->setPricingConfiguration($configuration);

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
            $this->thereIsTaxRate($data['amount'], $data['name'], $data['category'], $data['zone'], isset($data['included in price?']) ? $data['included in price?'] : false, false);
        }

        $this->getEntityManager()->flush();
    }

    /**
     * @Given /^there is (\d+)% tax "([^""]*)" for category "([^""]*)" within zone "([^""]*)"$/
     * @Given /^I created (\d+)% tax "([^""]*)" for category "([^""]*)" within zone "([^""]*)"$/
     */
    public function thereIsTaxRate($amount, $name, $category, $zone, $includedInPrice = false, $flush = true)
    {
        /* @var $rate TaxRateInterface */
        $rate = $this->getRepository('tax_rate')->createNew();
        $rate->setName($name);
        $rate->setAmount($amount / 100);
        $rate->setIncludedInPrice($includedInPrice);
        $rate->setCategory($this->findOneByName('tax_category', $category));
        $rate->setZone($this->findOneByName('zone', $zone));
        $rate->setCalculator('default');

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
            $calculator = array_key_exists('calculator', $data) ? str_replace(' ', '_', strtolower($data['calculator'])) : DefaultCalculators::PER_ITEM_RATE;
            $configuration = array_key_exists('configuration', $data) ? $this->getConfiguration($data['configuration']) : null;

            if (!isset($data['enabled'])) {
                $data['enabled'] = 'yes';
            }

            $this->thereIsShippingMethod($data['name'], $data['zone'], $calculator, $configuration, 'yes' === $data['enabled'], false);
        }

        $this->getEntityManager()->flush();
    }

    /**
     * @Given /^I created shipping method "([^""]*)" within zone "([^""]*)"$/
     * @Given /^There is shipping method "([^""]*)" within zone "([^""]*)"$/
     */
    public function thereIsShippingMethod($name, $zoneName, $calculator = DefaultCalculators::PER_ITEM_RATE, array $configuration = null, $enabled = true, $flush = true)
    {
        /* @var $method ShippingMethodInterface */
        $method = $this
            ->getRepository('shipping_method')
            ->createNew()
        ;

        $method->setName($name);
        $method->setZone($this->findOneByName('zone', $zoneName));
        $method->setCalculator($calculator);
        $method->setConfiguration($configuration ?: array('amount' => 2500));
        $method->setEnabled($enabled);

        $manager = $this->getEntityManager();
        $manager->persist($method);
        if ($flush) {
            $manager->flush();
        }

        return $method;
    }

    /**
     * @Given /^the following locales are defined:$/
     * @Given /^there are following locales configured:$/
     */
    public function thereAreLocales(TableNode $table)
    {
        $repository = $this->getRepository('locale');
        $manager = $this->getEntityManager();

        foreach ($table->getHash() as $data) {
            $locale = $repository->createNew();
            $locale->setCode($data['code']);

            if (isset($data['enabled'])) {
                $locale->setEnabled('yes' === $data['enabled']);
            }

            $manager->persist($locale);
        }

        $manager->flush();
    }

    /**
     * @Given /^product "([^""]*)" is available in all variations$/
     */
    public function productIsAvailableInAllVariations($productName)
    {
        $product = $this->findOneByName('product', $productName);

        $this->getService('sylius.generator.product_variant')->generate($product);

        /* @var $variant ProductVariantInterface */
        foreach ($product->getVariants() as $variant) {
            $variant->setPrice($product->getMasterVariant()->getPrice());
        }

        $manager = $this->getEntityManager();
        $manager->persist($product);
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
        $addressData = explode(',', $string);
        $addressData = array_map('trim', $addressData);

        list($firstname, $lastname) = explode(' ', $addressData[0]);

        /* @var $address AddressInterface */
        $address = $this->getRepository('address')->createNew();
        $address->setFirstname(trim($firstname));
        $address->setLastname(trim($lastname));
        $address->setStreet($addressData[1]);
        $address->setPostcode($addressData[2]);
        $address->setCity($addressData[3]);
        $address->setCountry($this->findOneByName('country', $addressData[4]));

        return $address;
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
        $payment = $this->getRepository('payment')->createNew();
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
        $shippingMethod = $this->getRepository('shipping_method')->findOneBy(array('name' => $shipmentData[0]));

        /* @var $shipment ShipmentInterface */
        $shipment = $this->getRepository('shipment')->createNew();
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
    private function iAmLoggedInAsRole($role, $email = 'sylius@example.com', array $authorizationRoles = array())
    {
        $this->thereIsUser($email, 'sylius', null, 'yes', null, array(), true, $authorizationRoles);
        $this->getSession()->visit($this->generatePageUrl('fos_user_security_login'));

        $this->fillField('Email', $email);
        $this->fillField('Password', 'sylius');
        $this->pressButton('login');
    }

    /**
     * @Given /^all products assigned to "([^""]*)" channel$/
     */
    public function assignChannelToProducts($code)
    {
        /** @var ChannelInterface $channel */
        $channel = $this->getRepository('channel')->findOneBy(array('code' => $code));
        /** @var ProductInterface[] $products */
        $products = $this->getRepository('product')->findAll();
        foreach ($products as $product) {
            $product->addChannel($channel);
        }

        $this->getEntityManager()->flush();
    }

    /**
     * @Given /^all promotions assigned to "([^""]*)" channel$/
     */
    public function assignChannelToPromotions($code)
    {
        /** @var ChannelInterface $channel */
        $channel = $this->getRepository('channel')->findOneBy(array('code' => $code));
        /** @var ChannelsAwareInterface[] $promotions */
        $promotions = $this->getRepository('promotion')->findAll();
        foreach ($promotions as $promotion) {
            $promotion->addChannel($channel);
        }

        $this->getEntityManager()->flush();
    }

    /**
     * @Given /^there is default channel configured$/
     */
    public function setupDefaultChannel()
    {
        $this->thereIsChannel('DEFAULT-WEB', "Default", "localhost");
    }

    /**
     * @Given /^there are following channels configured:$/
     */
    public function thereAreFollowingChannels(TableNode $table)
    {
        foreach ($table->getHash() as $data) {
            $this->thereIsChannel(
                $data['code'],
                $data['name'],
                isset($data['url']) ? $data['url'] : null,
                isset($data['locales']) ? $data['locales'] : null,
                isset($data['currencies']) ? $data['currencies'] : null,
                isset($data['shipping']) ? $data['shipping'] : null,
                isset($data['payments']) ? $data['payments'] : null,
                false
                );
        }

        $this->getEntityManager()->flush();
    }

    /**
     * @Given /^channel "([^""]*)" has following configuration:$/
     */
    public function channelHasFollowingConfiguration($code, TableNode $table)
    {
        $channel = $this->getRepository('channel')->findOneBy(array('code' => $code));

        foreach ($table->getHash() as $data) {
            $this->configureChannel(
                $channel,
                isset($data['locales']) ? $data['locales'] : null,
                isset($data['currencies']) ? $data['currencies'] : null,
                isset($data['shipping']) ? $data['shipping'] : null,
                isset($data['payment']) ? $data['shipping'] : null
            );
        }

        $this->getEntityManager()->flush();
    }

    /**
     * @Given /^There is channel "([^""]*)" named "([^""]*)" for url "([^""]*)"$/
     */
    public function thereIsChannel($code, $name, $url, $locales = null, $currencies = "EUR", $shippingMethods = null, $paymentMethos = null, $flush = true)
    {
        /* @var $channel ChannelInterface */
        $channel = $this->getRepository('channel')->createNew();
        $channel->setCode($code);
        $channel->setName($name);
        $channel->setUrl($url);

        $this->configureChannel($channel, $locales, $currencies, $shippingMethods, $paymentMethos);

        $manager = $this->getEntityManager();
        $manager->persist($channel);
        if ($flush) {
            $manager->flush();
        }

        return $channel;
    }

    private function configureChannel(ChannelInterface $channel, $localeCodes = null, $currencyCodes = null, $shippingMethodNames = null, $paymentMethodNames = null)
    {
        if ($shippingMethodNames) {
            $shippingMethodNames = array_map('trim', explode(',', $shippingMethodNames));
            foreach ($shippingMethodNames as $shippingMethodName) {
                $shippingMethod = $shippingMethods = $this->getRepository('shipping_method')->findOneBy(array('name' => $shippingMethodName));
                $channel->addShippingMethod($shippingMethod);
            }
        }

        if ($paymentMethodNames) {
            $paymentMethodNames = array_map('trim', explode(',', $paymentMethodNames));
            $paymentMethods = $this->getRepository('payment_method')->findBy(array('name' => $paymentMethodNames));
            foreach ($paymentMethods as $paymentMethod) {
                $channel->addPaymentMethod($paymentMethod);
            }
        }

        if ($localeCodes) {
            $localeCodes = array_map('trim', explode(',', $localeCodes));
            $locales = $this->getRepository('locale')->findBy(array('code' => $localeCodes));
            foreach($locales as $locale) {
                $channel->addLocale($locale);
            }
        }

        if ($currencyCodes) {
            $currencyCodes = array_map('trim', explode(',', $currencyCodes));
            $currencies = $this->getRepository('currency')->findBy(array('code' => $currencyCodes));
            foreach ($currencies as $currency) {
                $channel->addCurrency($currency);
            }
        }
    }
}
