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

use Sylius\Bundle\AdminBundle\Doctrine\Query\Taxon\AllTaxons;
use Sylius\Bundle\AdminBundle\Doctrine\Query\Taxon\AllTaxonsInterface;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services->set('sylius_admin.doctrine.query.taxon.all_taxons', AllTaxons::class)
        ->args([
            service('doctrine.orm.entity_manager'),
            service('sylius_admin.context.locale.admin_based'),
            service('sylius.translation_locale_provider'),
        ]);

    $services->alias(AllTaxonsInterface::class, 'sylius_admin.doctrine.query.taxon.all_taxons');
};
