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

namespace spec\Sylius\Bundle\ApiBundle\Doctrine\ORM\QueryExtension\Admin\Promotion\PromotionCoupon;

use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Post;
use Doctrine\ORM\QueryBuilder;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ApiBundle\Doctrine\ORM\QueryExtension\Admin\Promotion\PromotionCoupon\PostResultExtension;
use Sylius\Component\Core\Model\PromotionCouponInterface;

final class PostResultExtensionSpec extends ObjectBehavior
{
    function it_is_query_result_item_extension(): void
    {
        $this->shouldImplement(PostResultExtension::class);
    }

    function it_applies_nothing_to_item(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
    ): void {
        $this->applyToItem(
            $queryBuilder,
            $queryNameGenerator,
            'resourceClass',
            ['identifiers'],
        );
    }

    function it_does_not_support_if_operation_is_not_post(): void
    {
        $this->supportsResult(\stdClass::class, null, [])->shouldReturn(false);
    }

    function it_does_not_support_if_resource_class_is_not_promotion_coupon_interface(): void
    {
        $this->supportsResult(\stdClass::class, new Post(), [])->shouldReturn(false);
    }

    function it_supports_result_if_operation_is_post_and_resource_class_is_promotion_coupon_interface(): void
    {
        $this->supportsResult(PromotionCouponInterface::class, new Post(), [])->shouldReturn(true);
    }

    function it_returns_null_result(
        QueryBuilder $queryBuilder,
    ): void {
        $this->getResult($queryBuilder, PromotionCouponInterface::class, new Post(), [])->shouldReturn(null);
    }
}
