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

use Sylius\Bundle\UiBundle\Twig\MergeRecursiveExtension;
use Sylius\Bundle\UiBundle\Twig\PercentageExtension;
use Sylius\Bundle\UiBundle\Twig\RedirectPathExtension;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services->set('sylius.twig.extension.percentage', PercentageExtension::class)->tag('twig.extension');

    $services->set('sylius.twig.extension.merge_recursive', MergeRecursiveExtension::class)->tag('twig.extension');

    $services
        ->set('sylius.twig.extension.redirect_path', RedirectPathExtension::class)
        ->args([
            service('sylius.grid.filter_storage'),
            service('router'),
        ])
        ->tag('twig.extension')
    ;
};
