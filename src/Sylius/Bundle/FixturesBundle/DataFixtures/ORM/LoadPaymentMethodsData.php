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
        $manager->persist($this->createPaymentMethod('Dummy', 'dummy'));
        $manager->persist($this->createPaymentMethod('PaypalExpressCheckout', 'paypal_express_checkout'));
        $manager->persist($this->createPaymentMethod('Be2bill', 'be2bill_direct'));
        $manager->persist($this->createPaymentMethod('Be2billOffsite', 'be2bill_offsite'));
        $manager->persist($this->createPaymentMethod('StripeCheckout', 'stripe_checkout'));
        $manager->persist($this->createPaymentMethod('Offline', 'offline'));

        $manager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function getOrder()
    {
        return 4;
    }

    /**
     * Create payment method.
     *
     * @param string  $name
     * @param string  $gateway
     * @param Boolean $enabled
     *
     * @return PaymentMethodInterface
     */
    protected function createPaymentMethod($name, $gateway, $enabled = true)
    {
        /* @var $method PaymentMethodInterface */
        $method = $this->getPaymentMethodRepository()->createNew();
        $method->setName($name);
        $method->setGateway($gateway);
        $method->setEnabled($enabled);

        $this->setReference('Sylius.PaymentMethod.'.$name, $method);

        return $method;
    }
}
