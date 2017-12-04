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

namespace Sylius\Bundle\CoreBundle;

use Sylius\Bundle\CoreBundle\DependencyInjection\Compiler\LazyCacheWarmupPass;
use Sylius\Bundle\CoreBundle\DependencyInjection\Compiler\RegisterTaxCalculationStrategiesPass;
use Sylius\Bundle\CoreBundle\DependencyInjection\Compiler\TranslatableEntityLocalePass;
use Sylius\Bundle\ResourceBundle\AbstractResourceBundle;
use Sylius\Bundle\ResourceBundle\SyliusResourceBundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class SyliusCoreBundle extends AbstractResourceBundle
{
    /**
     * {@inheritdoc}
     */
    public function getSupportedDrivers(): array
    {
        return [
            SyliusResourceBundle::DRIVER_DOCTRINE_ORM,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $container->addCompilerPass(new LazyCacheWarmupPass());
        $container->addCompilerPass(new RegisterTaxCalculationStrategiesPass());
        $container->addCompilerPass(new TranslatableEntityLocalePass());
    }

    /**
     * {@inheritdoc}
     */
    protected function getModelNamespace(): string
    {
        return 'Sylius\Component\Core\Model';
    }
}
