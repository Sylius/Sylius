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

namespace spec\Sylius\Component\Core\Test\Factory;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Core\Model\PromotionInterface;
use Sylius\Component\Core\Test\Factory\TestCatalogPromotionFactoryInterface;
use Sylius\Component\Core\Test\Factory\TestPromotionFactoryInterface;
use Sylius\Component\Promotion\Model\CatalogPromotionInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

final class TestCatalogPromotionFactorySpec extends ObjectBehavior
{
    function let(FactoryInterface $catalogPromotionFactory): void
    {
        $this->beConstructedWith($catalogPromotionFactory);
    }

    function it_implements_a_test_catalog_promotion_factory_interface(): void
    {
        $this->shouldImplement(TestCatalogPromotionFactoryInterface::class);
    }

    function it_creates_a_catalog_promotion_with_a_given_name(
        $catalogPromotionFactory,
        CatalogPromotionInterface $catalogPromotion): void
    {
        $catalogPromotionFactory->createNew()->willReturn($catalogPromotion);
        $catalogPromotion->setName('Super promotion')->shouldBeCalled();
        $catalogPromotion->setCode('super_promotion')->shouldBeCalled();

        $this->create('Super promotion')->shouldReturn($catalogPromotion);
    }
}
