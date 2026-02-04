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

use Sylius\Bundle\TaxonomyBundle\Provider\TaxonTreeProvider;
use Sylius\Bundle\TaxonomyBundle\Validator\Constraints\TaxonParentRelationValidator;
use Sylius\Component\Taxonomy\Factory\TaxonFactory;
use Sylius\Component\Taxonomy\Factory\TaxonFactoryInterface;
use Sylius\Component\Taxonomy\Generator\TaxonSlugGenerator;
use Sylius\Component\Taxonomy\Generator\TaxonSlugGeneratorInterface;
use Sylius\Component\Taxonomy\Provider\TaxonTreeProviderInterface;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $container->import('services/form.php');
    $container->import('services/tree_repository.php');

    $services->set('sylius.custom_factory.taxon', TaxonFactory::class)
        ->decorate('sylius.factory.taxon', null, 256)
        ->args([service('sylius.custom_factory.taxon.inner')]);

    $services->alias(TaxonFactoryInterface::class, 'sylius.custom_factory.taxon');

    $services->set('sylius.generator.taxon_slug', TaxonSlugGenerator::class);

    $services->alias(TaxonSlugGeneratorInterface::class, 'sylius.generator.taxon_slug');

    $services->set('sylius.provider.taxon_tree', TaxonTreeProvider::class)
        ->args([
            service('sylius.repository.taxon'),
            service('sylius.repository.nested_tree.taxon'),
        ]);

    $services->alias(TaxonTreeProviderInterface::class, 'sylius.provider.taxon_tree');

    $services->set('sylius.validator.taxon_parent_relation', TaxonParentRelationValidator::class)
        ->tag('validator.constraint_validator', ['alias' => 'sylius_taxon_parent_relation_validator']);
};
