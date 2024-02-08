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

namespace Sylius\Bundle\AttributeBundle;

use Sylius\Bundle\AttributeBundle\DependencyInjection\Compiler\RegisterAttributeFactoryPass;
use Sylius\Bundle\AttributeBundle\DependencyInjection\Compiler\RegisterAttributeTypePass;
use Sylius\Bundle\ResourceBundle\AbstractResourceBundle;
use Sylius\Bundle\ResourceBundle\SyliusResourceBundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class SyliusAttributeBundle extends AbstractResourceBundle
{
    public function getSupportedDrivers(): array
    {
        return [
            SyliusResourceBundle::DRIVER_DOCTRINE_ORM,
        ];
    }

    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $container->addCompilerPass(new RegisterAttributeTypePass());
        $container->addCompilerPass(new RegisterAttributeFactoryPass());
    }

    protected function getModelNamespace(): string
    {
        return 'Sylius\Component\Attribute\Model';
    }
}
