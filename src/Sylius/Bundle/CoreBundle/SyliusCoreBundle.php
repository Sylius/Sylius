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

use Sylius\Bundle\CoreBundle\DependencyInjection\Compiler\DoctrineSluggablePass;
use Sylius\Bundle\CoreBundle\DependencyInjection\Compiler\LazyCacheWarmupPass;
use Sylius\Bundle\CoreBundle\DependencyInjection\Compiler\RoutingRepositoryPass;
use Sylius\Bundle\ResourceBundle\AbstractResourceBundle;
use Sylius\Bundle\ResourceBundle\SyliusResourceBundle;
use Sylius\Component\Core\Model\ProductVariantImageInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Sylius core bundle.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
class SyliusCoreBundle extends AbstractResourceBundle
{
    /**
     * {@inheritdoc}
     */
    public static function getSupportedDrivers()
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

        $container->addCompilerPass(new DoctrineSluggablePass());
        $container->addCompilerPass(new RoutingRepositoryPass());
        $container->addCompilerPass(new LazyCacheWarmupPass());
    }

    /**
     * {@inheritdoc}
     */
    protected function getModelInterfaces()
    {
        return [
            ProductVariantImageInterface::class => 'sylius.model.product_variant_image.class',
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function getModelNamespace()
    {
        return 'Sylius\Component\Core\Model';
    }
}
