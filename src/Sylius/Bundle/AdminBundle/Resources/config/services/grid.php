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

use Sylius\Bundle\AdminBundle\Form\Type\Grid\Filter\UxAutocompleteFilterType;
use Sylius\Bundle\AdminBundle\Form\Type\Grid\Filter\UxTranslatableAutocompleteFilterType;
use Sylius\Component\Grid\Filter\EntityFilter;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services->set('sylius_admin.grid_filter.ux_autocomplete', EntityFilter::class)
        ->tag('sylius.grid_filter', ['type' => 'ux_autocomplete', 'form_type' => UxAutocompleteFilterType::class]);

    $services->set('sylius_admin.grid_filter.ux_translatable_autocomplete', EntityFilter::class)
        ->tag('sylius.grid_filter', ['type' => 'ux_translatable_autocomplete', 'form_type' => UxTranslatableAutocompleteFilterType::class]);
};
