<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\TaxationBundle;

use Sylius\Bundle\ResourceBundle\AbstractResourceBundle;
use Sylius\Bundle\ResourceBundle\SyliusResourceBundle;
use Sylius\Bundle\TaxationBundle\DependencyInjection\Compiler\RegisterCalculatorsPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Taxation system for ecommerce Symfony2 applications.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class SyliusTaxationBundle extends AbstractResourceBundle
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
            'ROLE_SYLIUS_ADMIN'          => array(
                'ROLE_SYLIUS_TAXATION_ADMIN',
                'ROLE_SYLIUS_TAX_CATEGORY_ADMIN',
                'ROLE_SYLIUS_TAX_RATE_ADMIN',
            ),
            'ROLE_SYLIUS_TAXATION_ADMIN' => array(
                'ROLE_SYLIUS_TAXATION_LIST',
                'ROLE_SYLIUS_TAXATION_SHOW',
                'ROLE_SYLIUS_TAXATION_CREATE',
                'ROLE_SYLIUS_TAXATION_UPDATE',
                'ROLE_SYLIUS_TAXATION_DELETE',
            ),
            'ROLE_SYLIUS_TAX_CATEGORY_ADMIN' => array(
                'ROLE_SYLIUS_TAX_CATEGORY_LIST',
                'ROLE_SYLIUS_TAX_CATEGORY_SHOW',
                'ROLE_SYLIUS_TAX_CATEGORY_CREATE',
                'ROLE_SYLIUS_TAX_CATEGORY_UPDATE',
                'ROLE_SYLIUS_TAX_CATEGORY_DELETE',
            ),
            'ROLE_SYLIUS_TAX_RATE_ADMIN' => array(
                'ROLE_SYLIUS_TAX_RATE_LIST',
                'ROLE_SYLIUS_TAX_RATE_SHOW',
                'ROLE_SYLIUS_TAX_RATE_CREATE',
                'ROLE_SYLIUS_TAX_RATE_UPDATE',
                'ROLE_SYLIUS_TAX_RATE_DELETE',
            ),
        );
    }

    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new RegisterCalculatorsPass());
    }

    /**
     * {@inheritdoc}
     */
    protected function getModelInterfaces()
    {
        return array(
            'Sylius\Component\Taxation\Model\TaxCategoryInterface' => 'sylius.model.tax_category.class',
            'Sylius\Component\Taxation\Model\TaxRateInterface'     => 'sylius.model.tax_rate.class',
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function getModelNamespace()
    {
        return 'Sylius\Component\Taxation\Model';
    }
}
