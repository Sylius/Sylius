<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle;

use Sylius\Bundle\CoreBundle\DependencyInjection\Compiler\LazyCacheWarmupPass;
use Sylius\Bundle\CoreBundle\DependencyInjection\Compiler\RegisterTaxCalculationStrategiesPass;
use Sylius\Bundle\CoreBundle\DependencyInjection\Compiler\TranslatableEntityLocalePass;
use Sylius\Bundle\ResourceBundle\AbstractResourceBundle;
use Sylius\Bundle\ResourceBundle\SyliusResourceBundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 * @author Mark McKelvie <mark.mckelvie@reiss.com>
 */
final class SyliusCoreBundle extends AbstractResourceBundle
{
    /**
     * {@inheritdoc}
     */
    public function getSupportedDrivers()
    {
        return [
            SyliusResourceBundle::DRIVER_DOCTRINE_ORM,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new LazyCacheWarmupPass());
        $container->addCompilerPass(new RegisterTaxCalculationStrategiesPass());
        $container->addCompilerPass(new TranslatableEntityLocalePass());
    }

    /**
     * {@inheritdoc}
     */
    protected function getModelNamespace()
    {
        return 'Sylius\Component\Core\Model';
    }
}
