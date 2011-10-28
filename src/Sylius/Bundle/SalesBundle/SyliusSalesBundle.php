<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\SalesBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Sylius\Bundle\SalesBundle\DependencyInjection\Compiler\RegisterOperationsPass;

/**
 * Bundle of sales system.
 * 
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class SyliusSalesBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new RegisterOperationsPass());
    }
}
