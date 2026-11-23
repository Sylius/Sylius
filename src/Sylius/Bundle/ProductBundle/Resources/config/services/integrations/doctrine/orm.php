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

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Sylius\Bundle\ProductBundle\Doctrine\ORM\ProductOptionRepository;
use Sylius\Bundle\ProductBundle\Doctrine\ORM\ProductRepository;
use Sylius\Bundle\ProductBundle\Doctrine\ORM\ProductVariantRepository;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;

return static function (ContainerConfigurator $container) {
    $parameters = $container->parameters();
    $parameters->set('sylius.repository.product.class', ProductRepository::class);
    $parameters->set('sylius.repository.product_variant.class', ProductVariantRepository::class);
    $parameters->set('sylius.repository.product_attribute.class', EntityRepository::class);
    $parameters->set('sylius.repository.product_option.class', ProductOptionRepository::class);
};
