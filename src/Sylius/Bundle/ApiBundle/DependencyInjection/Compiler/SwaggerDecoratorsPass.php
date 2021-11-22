<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\ApiBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class SwaggerDecoratorsPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if ($container->getParameter('api_platform.enable_swagger_ui') === false && $container->getParameter('api_platform.enable_re_doc') === false) {
            $services = $container->findTaggedServiceIds('sylius.swagger.normalizer.documentation');

            foreach ($services as $serviceId => $serviceConfig) {
                $container->removeDefinition($serviceId);
            }
        }
    }
}
