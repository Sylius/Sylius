<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\AffiliateBundle;

use Sylius\Bundle\ResourceBundle\AbstractResourceBundle;
use Sylius\Bundle\ResourceBundle\SyliusResourceBundle;

/**
 * @author Joseph Bielawski <stloyd@gmail.com>
 */
class SyliusAffiliateBundle extends AbstractResourceBundle
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
    protected function getModelInterfaces()
    {
        return array(
            'Sylius\Component\Affiliate\Model\AffiliateInterface'   => 'sylius.model.affiliate.class',
            'Sylius\Component\Affiliate\Model\InvitationInterface'  => 'sylius.model.invitation.class',
            'Sylius\Component\Affiliate\Model\TransactionInterface' => 'sylius.model.transaction.class',
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function getModelNamespace()
    {
        return 'Sylius\Component\Affiliate\Model';
    }
}
