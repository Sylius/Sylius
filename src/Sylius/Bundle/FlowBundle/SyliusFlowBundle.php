<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\FlowBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

use Sylius\Bundle\FlowBundle\DependencyInjection\Compiler\RegisterScenariosPass;
use Sylius\Bundle\FlowBundle\DependencyInjection\Compiler\RegisterSessionBagsPass;
use Sylius\Bundle\FlowBundle\DependencyInjection\Compiler\RegisterStepsPass;

/**
 * Multiple action flows for Symfony2.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class SyliusFlowBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new RegisterScenariosPass());
        $container->addCompilerPass(new RegisterStepsPass());
        $container->addCompilerPass(new RegisterSessionBagsPass());
    }
}
