<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ContactBundle;

use Sylius\Bundle\ResourceBundle\AbstractResourceBundle;
use Sylius\Bundle\ResourceBundle\SyliusResourceBundle;

/**
 * Contact bundle.
 *
 * @author Michał Marcinkowski <michal.marcinkowski@lakion.com>
 */
class SyliusContactBundle extends AbstractResourceBundle
{
    /**
     * {@inheritdoc}
     */
    public static function getSupportedDrivers()
    {
        return array(
            SyliusResourceBundle::DRIVER_DOCTRINE_ORM,
        );
    }

    /**
     * {@inheritdoc}
     */
    public static function getSecurityRoles()
    {
        return array(
            'ROLE_SYLIUS_ADMIN' => array(
                'ROLE_SYLIUS_CONTACT_REQUEST_ADMIN',
                'ROLE_SYLIUS_CONTACT_TOPIC_ADMIN',
            ),
            'ROLE_SYLIUS_CONTACT_REQUEST_ADMIN' => array(
                'ROLE_SYLIUS_CONTACT_REQUEST_LIST',
                'ROLE_SYLIUS_CONTACT_REQUEST_SHOW',
                'ROLE_SYLIUS_CONTACT_REQUEST_CREATE',
                'ROLE_SYLIUS_CONTACT_REQUEST_UPDATE',
                'ROLE_SYLIUS_CONTACT_REQUEST_DELETE',
            ),
            'ROLE_SYLIUS_CONTACT_TOPIC_ADMIN' => array(
                'ROLE_SYLIUS_CONTACT_TOPIC_LIST',
                'ROLE_SYLIUS_CONTACT_TOPIC_SHOW',
                'ROLE_SYLIUS_CONTACT_TOPIC_CREATE',
                'ROLE_SYLIUS_CONTACT_TOPIC_UPDATE',
                'ROLE_SYLIUS_CONTACT_TOPIC_DELETE',
            ),
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function getModelInterfaces()
    {
        return array(
            'Sylius\Component\Contact\Model\RequestInterface' => 'sylius.model.contact_request.class',
            'Sylius\Component\Contact\Model\TopicInterface'   => 'sylius.model.contact_topic.class',
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function getModelNamespace()
    {
        return 'Sylius\Component\Contact\Model';
    }
}
