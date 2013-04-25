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
        $manager->persist($this->createPaymentMethod('Test Credit Card', 'dummy'));
        $manager->persist($this->createPaymentMethod('Stripe', 'dummy'));
        $manager->persist($this->createPaymentMethod('Disabled Payment method', 'dummy'), false);

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
