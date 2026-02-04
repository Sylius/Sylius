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

use Sylius\Bundle\UiBundle\Twig\Ux\ComponentTemplateFinder;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services->set('sylius.twig.ux.component_template_finder', ComponentTemplateFinder::class)
        ->decorate('ux.twig_component.component_template_finder')
        ->args([
            service('.inner'),
            service('twig.loader.native_filesystem'),
            '%sylius_ui.twig_ux.anonymous_component_template_prefixes%',
        ]);
};
