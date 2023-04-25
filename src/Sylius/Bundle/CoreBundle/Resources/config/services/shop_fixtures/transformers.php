<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Sylius\Bundle\CoreBundle\ShopFixtures\Transformer\CustomerTransformer;
use Sylius\Bundle\CoreBundle\ShopFixtures\Transformer\CustomerTransformerInterface;

return static function (ContainerConfigurator $container) {
    $container->services()

        ->set('sylius.shop_fixtures.transformer.customer', CustomerTransformer::class)
        ->alias(CustomerTransformerInterface::class, 'sylius.shop_fixtures.transformer.customer')

    ;
};
