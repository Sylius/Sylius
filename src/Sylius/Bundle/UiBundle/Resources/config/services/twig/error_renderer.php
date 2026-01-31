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

use Sylius\Bundle\UiBundle\Twig\ErrorRenderer\TwigErrorRenderer;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services->set('sylius.twig.error_renderer', TwigErrorRenderer::class)
        ->decorate('twig.error_renderer.html', null, 64)
        ->args([
            service('.inner'),
            service('twig'),
            tagged_iterator('sylius.twig.error_template_finder'),
            '%kernel.debug%',
        ]);
};
