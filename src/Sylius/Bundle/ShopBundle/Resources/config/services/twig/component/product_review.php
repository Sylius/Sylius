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

use Sylius\Bundle\ShopBundle\Form\Type\Product\ProductReviewType;
use Sylius\Bundle\ShopBundle\Twig\Component\ProductReview\CountComponent;
use Sylius\Bundle\ShopBundle\Twig\Component\ProductReview\ListComponent;
use Sylius\Bundle\UiBundle\Twig\Component\ResourceFormComponent;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services->set('sylius_shop.twig.component.product_review.list', ListComponent::class)
        ->args([service('sylius.repository.product_review')])
        ->tag('sylius.twig_component', ['key' => 'sylius_shop:product_review:list']);

    $services->set('sylius_shop.twig.component.product_review.count', CountComponent::class)
        ->args([service('sylius.repository.product_review')])
        ->tag('sylius.twig_component', ['key' => 'sylius_shop:product_review.count']);

    $services->set('sylius_shop.twig.component.product_review.form', ResourceFormComponent::class)
        ->args([
            service('sylius.repository.product_review'),
            service('form.factory'),
            '%sylius.model.product_review.class%',
            ProductReviewType::class,
        ])
        ->tag('sylius.live_component.shop', ['key' => 'sylius_shop:product_review:form']);
};
