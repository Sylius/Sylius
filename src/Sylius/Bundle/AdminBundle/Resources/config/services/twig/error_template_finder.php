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

use Sylius\Bundle\AdminBundle\Twig\ErrorTemplateFinder\ErrorTemplateFinder;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services->set('sylius_admin.twig.error_template_finder', ErrorTemplateFinder::class)
        ->args([
            service('sylius.section_resolver.uri_based'),
            service('sylius_admin.provider.logged_in_admin_user'),
            service('twig'),
        ])
        ->tag('sylius.twig.error_template_finder');
};
