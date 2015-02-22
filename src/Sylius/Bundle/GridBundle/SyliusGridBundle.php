<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\GridBundle;

use Sylius\Bundle\GridBundle\DependencyInjection\Compiler\RegisterFiltersPass;
use Sylius\Bundle\GridBundle\DependencyInjection\Compiler\RegisterColumnTypesPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Sylius grid bundle.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class SyliusGridBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new RegisterFiltersPass());
        $container->addCompilerPass(new RegisterColumnTypesPass());
    }
}
