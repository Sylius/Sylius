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

namespace spec\Sylius\Component\Promotion\Model;

use Doctrine\Common\Collections\Collection;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Promotion\Model\CatalogPromotionActionInterface;
use Sylius\Component\Promotion\Model\CatalogPromotionInterface;
use Sylius\Component\Promotion\Model\CatalogPromotionScopeInterface;

final class CatalogPromotionSpec extends ObjectBehavior
{
    public function let()
    {
        $this->setCurrentLocale('en_US');
        $this->setFallbackLocale('en_US');
    }

    function it_implements_catalog_promotion_interface(): void
    {
        $this->shouldImplement(CatalogPromotionInterface::class);
    }

    function it_has_no_id_by_default(): void
    {
        $this->getId()->shouldReturn(null);
    }

    function its_code_is_mutable(): void
    {
        $this->setCode('mugs_discount');
        $this->getCode()->shouldReturn('mugs_discount');
    }

    function its_name_is_mutable(): void
    {
        $this->setName('Mugs discount');
        $this->getName()->shouldReturn('Mugs discount');
    }

    function its_label_is_mutable(): void
    {
        $this->setLabel('Mugs discount');
        $this->getLabel()->shouldReturn('Mugs discount');
    }

    function its_description_is_mutable(): void
    {
        $this->setDescription('Discount on every mug.');
        $this->getDescription()->shouldReturn('Discount on every mug.');
    }

    function it_is_a_catalog_promotion(): void
    {
        $this->shouldImplement(CatalogPromotionInterface::class);
    }

    function it_does_not_have_id_by_default(): void
    {
        $this->getId()->shouldReturn(null);
    }

    function it_has_mutable_code(): void
    {
        $this->setCode('mug_catalog_promotion');
        $this->getCode()->shouldReturn('mug_catalog_promotion');
    }

    function its_start_date_is_mutable(): void
    {
        $this->setStartDate(new \DateTime('2021-01-01'));
        $this->getStartDate()->shouldBeLike(new \DateTime('2021-01-01'));
    }

    function its_end_date_is_mutable(): void
    {
        $this->setEndDate(new \DateTime('2021-01-05'));
        $this->getEndDate()->shouldBeLike(new \DateTime('2021-01-05'));
    }

    function its_priority_is_mutable(): void
    {
        $this->getPriority()->shouldReturn(0);
        $this->setPriority(200);
        $this->getPriority()->shouldReturn(200);
    }

    function it_initializes_scopes_collection_by_default(): void
    {
        $this->getScopes()->shouldHaveType(Collection::class);
    }

    function it_adds_scopes(CatalogPromotionScopeInterface $scope): void
    {
        $this->hasScope($scope)->shouldReturn(false);

        $scope->setCatalogPromotion($this)->shouldBeCalled();
        $this->addScope($scope);

        $this->hasScope($scope)->shouldReturn(true);
    }

    function it_removes_scopes(CatalogPromotionScopeInterface $scope): void
    {
        $this->hasscope($scope)->shouldReturn(false);

        $scope->setCatalogPromotion($this)->shouldBeCalled();
        $this->addScope($scope);

        $scope->setCatalogPromotion(null)->shouldBeCalled();
        $this->removeScope($scope);

        $this->hasScope($scope)->shouldReturn(false);
    }

    function it_adds_actions(CatalogPromotionActionInterface $action): void
    {
        $this->hasAction($action)->shouldReturn(false);

        $action->setCatalogPromotion($this)->shouldBeCalled();
        $this->addAction($action);

        $this->hasAction($action)->shouldReturn(true);
    }

    function it_removes_actions(CatalogPromotionActionInterface $action): void
    {
        $this->hasAction($action)->shouldReturn(false);

        $action->setCatalogPromotion($this)->shouldBeCalled();
        $this->addAction($action);

        $action->setCatalogPromotion(null)->shouldBeCalled();
        $this->removeAction($action);

        $this->hasAction($action)->shouldReturn(false);
    }
}
