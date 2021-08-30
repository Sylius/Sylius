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

namespace spec\Sylius\Component\Promotion\Model;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Promotion\Model\CatalogPromotionInterface;
use Doctrine\Common\Collections\Collection;
use Sylius\Component\Promotion\Model\CatalogPromotionRuleInterface;

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

    function it_initializes_rules_collection_by_default(): void
    {
        $this->getRules()->shouldHaveType(Collection::class);
    }

    function it_adds_rules(CatalogPromotionRuleInterface $rule): void
    {
        $this->hasRule($rule)->shouldReturn(false);

        $rule->setCatalogPromotion($this)->shouldBeCalled();
        $this->addRule($rule);

        $this->hasRule($rule)->shouldReturn(true);
    }

    function it_removes_rules(CatalogPromotionRuleInterface $rule): void
    {
        $this->hasRule($rule)->shouldReturn(false);

        $rule->setCatalogPromotion($this)->shouldBeCalled();
        $this->addRule($rule);

        $rule->setCatalogPromotion(null)->shouldBeCalled();
        $this->removeRule($rule);

        $this->hasRule($rule)->shouldReturn(false);
    }
}
