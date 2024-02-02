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

namespace Sylius\Bundle\ShopBundle;

use Sylius\Bundle\ShopBundle\DependencyInjection\Compiler\BackwardsCompatibility\ReplaceEmailManagersPass;
use Sylius\Bundle\ShopBundle\DependencyInjection\Compiler\LogoutListenerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class SyliusShopBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new LogoutListenerPass());
        $container->addCompilerPass(new ReplaceEmailManagersPass());
    }
}
