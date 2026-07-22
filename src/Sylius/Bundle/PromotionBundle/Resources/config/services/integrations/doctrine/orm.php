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

use Sylius\Bundle\PromotionBundle\Doctrine\ORM\CatalogPromotionRepository;
use Sylius\Bundle\PromotionBundle\Doctrine\ORM\PromotionCouponRepository;
use Sylius\Bundle\PromotionBundle\Doctrine\ORM\PromotionRepository;

return static function (ContainerConfigurator $container) {
    $parameters = $container->parameters();
    $parameters->set('sylius.repository.catalog_promotion.class', CatalogPromotionRepository::class);
    $parameters->set('sylius.repository.promotion.class', PromotionRepository::class);
    $parameters->set('sylius.repository.promotion_coupon.class', PromotionCouponRepository::class);
};
