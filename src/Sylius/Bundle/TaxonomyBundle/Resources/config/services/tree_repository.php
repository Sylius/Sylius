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

use Doctrine\ORM\Mapping\ClassMetadata;
use Gedmo\Tree\Entity\Repository\NestedTreeRepository;
use Sylius\Bundle\TaxonomyBundle\Repository\TaxonTreeRepository;
use Sylius\Bundle\TaxonomyBundle\Repository\TaxonTreeRepositoryInterface;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services->set('sylius.custom_repository.tree.taxon', TaxonTreeRepository::class)
        ->decorate('sylius.repository.tree.taxon')
        ->args([service('.inner')]);

    $services->alias(TaxonTreeRepositoryInterface::class, 'sylius.custom_repository.tree.taxon');

    $services->set('sylius.repository.tree.taxon', NestedTreeRepository::class)
        ->args([
            service('doctrine.orm.entity_manager'),
            inline_service(ClassMetadata::class)
                ->args(['%sylius.model.taxon.class%'])
                ->factory([service('doctrine.orm.entity_manager'), 'getClassMetadata']),
        ]);

    $services->set('sylius.repository.nested_tree.taxon', NestedTreeRepository::class)
        ->args([
            service('doctrine.orm.entity_manager'),
            inline_service(ClassMetadata::class)
                ->args(['%sylius.model.taxon.class%'])
                ->factory([service('doctrine.orm.entity_manager'), 'getClassMetadata']),
        ]);
};
