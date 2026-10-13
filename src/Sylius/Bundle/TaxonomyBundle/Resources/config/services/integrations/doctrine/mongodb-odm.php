<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Sylius\Bundle\ResourceBundle\Doctrine\ODM\MongoDB\DocumentRepository;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services
        ->set('sylius.doctrine.odm.mongodb.unit_of_work', 'Doctrine\ODM\MongoDB\UnitOfWork')
        ->factory([service('doctrine.odm.mongodb.document_manager'), 'getUnitOfWork'])
    ;

    $services->alias('sylius.manager.taxon', 'doctrine.odm.mongodb.document_manager');

    $services
        ->set('sylius.doctrine.odm.mongodb.metadata.taxon', 'Doctrine\ODM\MongoDB\Mapping\ClassMetadata')
        ->args(['%sylius.model.taxon.class%'])
        ->factory([service('sylius.manager.taxon'), 'getClassMetadata'])
    ;

    $services
        ->set('sylius.repository.taxon', DocumentRepository::class)
        ->args([
            service('sylius.manager.taxon'),
            service('sylius.doctrine.odm.mongodb.unit_of_work'),
            service('sylius.doctrine.odm.mongodb.metadata.taxon'),
        ])
    ;
};
