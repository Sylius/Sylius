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

namespace Tests\Sylius\Bundle\ApiBundle\Doctrine\ORM\QueryExtension\Admin\Promotion\PromotionCoupon;

use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Post;
use Doctrine\ORM\QueryBuilder;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\ApiBundle\Doctrine\ORM\QueryExtension\Admin\Promotion\PromotionCoupon\PostResultExtension;
use Sylius\Bundle\ApiBundle\SectionResolver\AdminApiSection;
use Sylius\Bundle\ApiBundle\SectionResolver\ShopApiSection;
use Sylius\Bundle\CoreBundle\SectionResolver\SectionProviderInterface;
use Sylius\Component\Core\Model\PromotionCouponInterface;

final class PostResultExtensionTest extends TestCase
{
    private PostResultExtension $extension;

    /** @var MockObject&SectionProviderInterface */
    private $sectionProvider;

    protected function setUp(): void
    {
        $this->sectionProvider = $this->createMock(SectionProviderInterface::class);
        $this->extension = new PostResultExtension($this->sectionProvider);
    }

    public function test_it_is_a_post_result_extension(): void
    {
        $this->assertInstanceOf(PostResultExtension::class, $this->extension);
    }

    public function test_it_applies_nothing_to_item(): void
    {
        $queryBuilder = $this->createMock(QueryBuilder::class);
        $queryNameGenerator = $this->createMock(QueryNameGeneratorInterface::class);

        $this->extension->applyToItem(
            $queryBuilder,
            $queryNameGenerator,
            'resourceClass',
            ['identifiers'],
        );

        $this->addToAssertionCount(1);
    }

    public function test_it_does_not_support_if_operation_is_not_post(): void
    {
        $this->assertFalse($this->extension->supportsResult(\stdClass::class, null, []));
    }

    public function test_it_does_not_support_if_section_is_not_admin(): void
    {
        $shopSection = $this->createMock(ShopApiSection::class);
        $this->sectionProvider->method('getSection')->willReturn($shopSection);

        $this->assertFalse($this->extension->supportsResult(\stdClass::class, new Post(), []));
    }

    public function test_it_does_not_support_if_resource_class_is_not_promotion_coupon_interface(): void
    {
        $this->assertFalse($this->extension->supportsResult(\stdClass::class, new Post(), []));
    }

    public function test_it_supports_result_if_post_and_promotion_coupon_interface_and_admin_section(): void
    {
        $adminSection = $this->createMock(AdminApiSection::class);
        $this->sectionProvider->method('getSection')->willReturn($adminSection);

        $this->assertTrue($this->extension->supportsResult(PromotionCouponInterface::class, new Post(), []));
    }

    public function test_it_returns_null_result(): void
    {
        $queryBuilder = $this->createMock(QueryBuilder::class);

        $result = $this->extension->getResult(
            $queryBuilder,
            PromotionCouponInterface::class,
            new Post(),
            [],
        );

        $this->assertNull($result);
    }
}
