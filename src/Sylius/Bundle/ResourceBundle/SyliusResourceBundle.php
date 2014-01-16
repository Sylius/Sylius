<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ResourceBundle;

use Sylius\Bundle\ResourceBundle\DependencyInjection\Factory\DoctrineODMFactory;
use Sylius\Bundle\ResourceBundle\DependencyInjection\Factory\DoctrineORMFactory;
use Sylius\Bundle\ResourceBundle\DependencyInjection\Factory\DoctrinePHPCRFactory;
use Sylius\Bundle\ResourceBundle\DependencyInjection\SyliusResourceExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Resource bundle.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@sylius.pl>
 */
class SyliusResourceBundle extends Bundle
{
    // Bundle driver list.
    const DRIVER_DOCTRINE_ORM         = 'doctrine/orm';
    const DRIVER_DOCTRINE_MONGODB_ODM = 'doctrine/mongodb-odm';
    const DRIVER_DOCTRINE_PHPCR_ODM   = 'doctrine/phpcr-odm';

    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        /** @var SyliusResourceExtension $extension */
        $extension = $container->getExtension('sylius_resource');
        $extension->addDatabaseDriverFactory(new DoctrineORMFactory($container));
        $extension->addDatabaseDriverFactory(new DoctrineODMFactory($container));
        $extension->addDatabaseDriverFactory(new DoctrinePHPCRFactory($container));
    }
}
