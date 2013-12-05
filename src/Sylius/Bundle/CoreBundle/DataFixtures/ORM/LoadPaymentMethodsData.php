<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;

/**
 * Sample payment methods.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class LoadPaymentMethodsData extends DataFixture
{
    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $manager->persist($this->createPaymentMethod('Dummy', 'dummy'));
        $manager->persist($this->createPaymentMethod('Paypal Express Checkout', 'paypal_express_checkout'));
        $manager->persist($this->createPaymentMethod('Stripe', 'stripe'));
        $manager->persist($this->createPaymentMethod('Be2bill', 'be2bill'));

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
    private function createPaymentMethod($name, $gateway, $enabled = true)
    {
        $method = $this
            ->getPaymentMethodRepository()
            ->createNew()
        ;

        $method->setName($name);
        $method->setGateway($gateway);
        $method->setEnabled($enabled);

        $this->setReference('Sylius.PaymentMethod.'.$name, $method);

        return $method;
    }
}
