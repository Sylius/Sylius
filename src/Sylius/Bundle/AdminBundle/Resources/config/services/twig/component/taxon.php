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

use Sylius\Bundle\AdminBundle\Form\Type\TaxonType;
use Sylius\Bundle\AdminBundle\Twig\Component\Taxon\DeleteComponent;
use Sylius\Bundle\AdminBundle\Twig\Component\Taxon\FormComponent;
use Sylius\Bundle\AdminBundle\Twig\Component\Taxon\TreeComponent;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services->set('sylius_admin.twig.component.taxon.delete', DeleteComponent::class)
        ->args([service('security.csrf.token_manager')])
        ->call('setLiveResponder', [service('ux.live_component.live_responder')])
        ->tag('sylius.live_component.admin', ['key' => 'sylius_admin:taxon:delete']);

    $services->set('sylius_admin.twig.component.taxon.form', FormComponent::class)
        ->args([
            service('sylius.repository.taxon'),
            service('form.factory'),
            '%sylius.model.taxon.class%',
            TaxonType::class,
            service('sylius_admin.generator.taxon_slug'),
        ])
        ->tag('sylius.live_component.admin', ['key' => 'sylius_admin:taxon:form']);

    $services->set('sylius_admin.twig.component.taxon.tree', TreeComponent::class)
        ->args([
            service('sylius_admin.doctrine.query.taxon.all_taxons'),
            service('sylius.repository.taxon'),
            service('sylius.manager.taxon'),
        ])
        ->tag('sylius.live_component.admin', ['key' => 'sylius_admin:taxon:tree']);
};
