<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Core\Promotion\Checker;

use Doctrine\Common\Collections\Collection;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Promotion\Checker\RuleCheckerInterface;
use Sylius\Component\Promotion\Exception\UnsupportedTypeException;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;

/**
 * @author Joseph Bielawski <stloyd@gmail.com>
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
final class TaxonRuleCheckerSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Core\Promotion\Checker\TaxonRuleChecker');
    }

    function it_is_rule_checker()
    {
        $this->shouldImplement(RuleCheckerInterface::class);
    }

    function it_recognizes_subject_as_eligible_if_product_taxon_is_matched(
        OrderInterface $subject,
        OrderItemInterface $item,
        ProductInterface $bastardSword,
        TaxonInterface $swords
    ) {
        $configuration = ['taxons' => ['swords']];

        $swords->getCode()->willReturn('swords');
        $bastardSword->getTaxons()->willReturn([$swords]);
        $item->getProduct()->willReturn($bastardSword);
        $subject->getItems()->willReturn([$item]);

        $this->isEligible($subject, $configuration)->shouldReturn(true);
    }

    function it_recognizes_subject_as_eligible_if_product_taxon_is_matched_to_one_of_required_taxons(
        OrderInterface $subject,
        OrderItemInterface $item,
        ProductInterface $bastardSword,
        TaxonInterface $swords
    ) {
        $configuration = ['taxons' => ['swords', 'axes']];

        $swords->getCode()->willReturn('swords');
        $bastardSword->getTaxons()->willReturn([$swords]);
        $item->getProduct()->willReturn($bastardSword);
        $subject->getItems()->willReturn([$item]);

        $this->isEligible($subject, $configuration)->shouldReturn(true);
    }

    function it_recognizes_subject_as_not_eligible_if_product_taxon_is_not_matched(
        OrderInterface $subject,
        OrderItemInterface $item,
        ProductInterface $reflexBow,
        TaxonInterface $bows
    ) {
        $configuration = ['taxons' => ['swords', 'axes']];

        $bows->getCode()->willReturn('bows');
        $reflexBow->getTaxons()->willReturn([$bows]);
        $item->getProduct()->willReturn($reflexBow);
        $subject->getItems()->willReturn([$item]);

        $this->isEligible($subject, $configuration)->shouldReturn(false);
    }

    function it_does_nothing_if_configuration_is_invalid(OrderInterface $subject)
    {
        $subject->getItems()->shouldNotBeCalled();

        $this->isEligible($subject, []);
    }

    function it_throws_exception_if_promotion_subject_is_not_order(
        Collection $taxonsCollection,
        PromotionSubjectInterface $subject
    ) {
        $this
            ->shouldThrow(new UnsupportedTypeException($subject->getWrappedObject(), OrderInterface::class))
            ->during('isEligible', [$subject, ['taxons' => $taxonsCollection]])
        ;
    }
}
