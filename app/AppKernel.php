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

use Sylius\Bundle\CoreBundle\Application\Kernel;

class AppKernel extends Kernel
{
    /**
     * {@inheritdoc}
     */
    public function registerBundles(): array
    {
        $bundles = [
            new \Sylius\Bundle\AdminBundle\SyliusAdminBundle(),
            new \Sylius\Bundle\ShopBundle\SyliusShopBundle(),

            new \FOS\OAuthServerBundle\FOSOAuthServerBundle(), // Required by SyliusAdminApiBundle.
            new \Sylius\Bundle\AdminApiBundle\SyliusAdminApiBundle(),
        ];

        return array_merge(parent::registerBundles(), $bundles);
    }
}
