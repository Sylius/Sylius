<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\NewsletterBundle;

use Sylius\Bundle\ResourceBundle\AbstractResourceBundle;
use Sylius\Bundle\ResourceBundle\SyliusResourceBundle;

/**
 * Newsletter bundle.
 *
 * @author Michał Marcinkowski <michal.marcinkowski@lakion.com>
 */
class SyliusNewsletterBundle extends AbstractResourceBundle
{
    /**
     * {@inheritdoc}
     */
    public static function getSupportedDrivers()
    {
        return array(
            SyliusResourceBundle::DRIVER_DOCTRINE_ORM
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function getBundlePrefix()
    {
        return 'sylius_newsletter';
    }

    /**
     * {@inheritdoc}
     */
    protected function getModelInterfaces()
    {
        return array(
            'Sylius\Component\Newsletter\Model\SubscriberInterface' => 'sylius.model.newsletter_subscriber.class',
            'Sylius\Component\Newsletter\Model\SubscriptionListInterface' => 'sylius.model.newsletter_subscription_list.class',
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function getModelNamespace()
    {
        return 'Sylius\Component\Newsletter\Model';
    }
}
