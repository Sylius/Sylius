<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\FixturesBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Bundle\FixturesBundle\DataFixtures\DataFixture;
use Sylius\Component\Payment\Model\PaymentMethodInterface;

/**
 * Sample payment methods.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class LoadPaymentMethodsData extends DataFixture
{
    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $manager->persist($this->createPaymentMethod('Dummy', 'dummy', 'fixed', array('amount' => 0)));
        $manager->persist($this->createPaymentMethod('PaypalExpressCheckout', 'paypal_express_checkout', 'fixed', array('amount' => 1000)));
        $manager->persist($this->createPaymentMethod('Be2bill', 'be2bill_direct', 'fixed', array('amount' => 100)));
        $manager->persist($this->createPaymentMethod('Be2billOffsite', 'be2bill_offsite', 'percent', array('amount' => 7)));
        $manager->persist($this->createPaymentMethod('StripeCheckout', 'stripe_checkout', 'percent', array('amount' => 5)));
        $manager->persist($this->createPaymentMethod('Offline', 'offline', 'fixed', array('amount' => 500)));

        $manager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function getOrder()
    {
        return 10;
    }

    /**
     * Create payment method.
     *
     * @param string  $name
     * @param string  $gateway
     * @param string  $feeCalculator
     * @param array   $feeCalculatorConfiguration
     * @param boolean $enabled
     *
     * @return PaymentMethodInterface
     */
    protected function createPaymentMethod($name, $gateway, $feeCalculator, array $feeCalculatorConfiguration, $enabled = true)
    {
        /* @var $method PaymentMethodInterface */
        $method = $this->getPaymentMethodRepository()->createNew();
        $method->setName($name);
        $method->setGateway($gateway);
        $method->setEnabled($enabled);
        $method->setFeeCalculator($feeCalculator);
        $method->setFeeCalculatorConfiguration($feeCalculatorConfiguration);

        $this->setReference('Sylius.PaymentMethod.'.$name, $method);

        return $method;
    }
}
