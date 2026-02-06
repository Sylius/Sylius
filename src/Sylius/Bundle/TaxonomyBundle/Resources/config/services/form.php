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

use Sylius\Bundle\TaxonomyBundle\Form\Type\TaxonPositionType;
use Sylius\Bundle\TaxonomyBundle\Form\Type\TaxonTranslationType;
use Sylius\Bundle\TaxonomyBundle\Form\Type\TaxonType;

return static function (ContainerConfigurator $container) {
    $parameters = $container->parameters();
    $services = $container->services();
    $parameters->set('sylius.form.type.taxon.validation_groups', ['sylius']);
    $parameters->set('sylius.form.type.taxon_translation.validation_groups', ['sylius']);
    $parameters->set('sylius.form.type.taxon_position.validation_groups', ['sylius']);

    $services
        ->set('sylius.form.type.taxon', TaxonType::class)
        ->args([
            '%sylius.model.taxon.class%',
            '%sylius.form.type.taxon.validation_groups%',
        ])
        ->tag('form.type')
    ;

    $services
        ->set('sylius.form.type.taxon_translation', TaxonTranslationType::class)
        ->args([
            '%sylius.model.taxon_translation.class%',
            '%sylius.form.type.taxon_translation.validation_groups%',
        ])
        ->tag('form.type')
    ;

    $services
        ->set('sylius.form.type.taxon_position', TaxonPositionType::class)
        ->args([
            '%sylius.model.taxon.class%',
            '%sylius.form.type.taxon_position.validation_groups%',
        ])
        ->tag('form.type')
    ;
};
