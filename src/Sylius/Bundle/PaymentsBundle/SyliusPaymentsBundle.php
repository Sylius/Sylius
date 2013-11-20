<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\PaymentsBundle;

use Doctrine\Bundle\DoctrineBundle\DependencyInjection\Compiler\DoctrineOrmMappingsPass;
use Sylius\Bundle\ResourceBundle\DependencyInjection\Compiler\ResolveDoctrineTargetEntitiesPass;
use Sylius\Bundle\ResourceBundle\SyliusResourceBundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Payments component for Symfony2 applications.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class SyliusPaymentsBundle extends Bundle
{
    /**
     * Return array of currently supported database drivers.
     *
     * @return array
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
    public function build(ContainerBuilder $container)
    {
        $interfaces = array(
            'Sylius\Bundle\PaymentsBundle\Model\CreditCardInterface'    => 'sylius.model.credit_card.class',
            'Sylius\Bundle\PaymentsBundle\Model\PaymentInterface'       => 'sylius.model.payment.class',
            'Sylius\Bundle\PaymentsBundle\Model\PaymentLogInterface'    => 'sylius.model.payment_log.class',
            'Sylius\Bundle\PaymentsBundle\Model\PaymentMethodInterface' => 'sylius.model.payment_method.class',
        );

        $container->addCompilerPass(new ResolveDoctrineTargetEntitiesPass('sylius_payments', $interfaces));

        $mappings = array(
            realpath(__DIR__.'/Resources/config/doctrine/model') => 'Sylius\Bundle\PaymentsBundle\Model',
        );

        $container->addCompilerPass(DoctrineOrmMappingsPass::createXmlMappingDriver($mappings, array('doctrine.orm.entity_manager'), 'sylius_payments.driver.doctrine/orm'));
    }
}
