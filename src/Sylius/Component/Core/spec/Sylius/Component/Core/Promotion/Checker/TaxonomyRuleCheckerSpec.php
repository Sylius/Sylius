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
use Sylius\Component\Core\Model\Taxon;

/**
 * @author Joseph Bielawski <stloyd@gmail.com>
 */
class TaxonomyRuleCheckerSpec extends ObjectBehavior
{
    function it_should_be_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Core\Promotion\Checker\TaxonomyRuleChecker');
    }

    function it_should_be_sylius_rule_checker()
    {
        $this->shouldImplement('Sylius\Component\Promotion\Checker\RuleCheckerInterface');
    }

    function it_should_recognize_subject_as_eligible_if_product_taxonomy_is_matched(
        OrderInterface $subject,
        OrderItemInterface $item,
        Taxon $taxon,
        ProductInterface $product,
        Collection $taxons
    ) {
        $configuration = array('taxons' => $taxons, 'exclude' => false);

        $taxons->contains(1)->willReturn(true);
        $taxon->getId()->willReturn(1);
        $product->getTaxons()->willReturn(array($taxon));
        $item->getProduct()->willReturn($product);
        $subject->getItems()->willReturn(array($item));

        $this->isEligible($subject, $configuration)->shouldReturn(true);
    }

    function it_should_recognize_subject_as_not_eligible_if_product_taxonomy_is_not_matched(
        OrderInterface $subject,
        OrderItemInterface $item,
        Taxon $taxon,
        ProductInterface $product,
        Collection $taxons
    ) {
        $configuration = array('taxons' => $taxons, 'exclude' => false);

        $taxons->contains(2)->willReturn(false);
        $taxon->getId()->willReturn(2);
        $product->getTaxons()->willReturn(array($taxon));
        $item->getProduct()->willReturn($product);
        $subject->getItems()->willReturn(array($item));

        $this->isEligible($subject, $configuration)->shouldReturn(false);
    }

    function it_should_recognize_subject_as_eligible_if_product_taxonomy_is_not_matched_and_exclude_is_set(
        OrderInterface $subject,
        OrderItemInterface $item,
        Taxon $taxon,
        ProductInterface $product,
        Collection $taxons
    ) {
        $configuration = array('taxons' => $taxons, 'exclude' => true);

        $taxons->contains(2)->willReturn(false);
        $taxon->getId()->willReturn(2);
        $product->getTaxons()->willReturn(array($taxon));
        $item->getProduct()->willReturn($product);
        $subject->getItems()->willReturn(array($item));

        $this->isEligible($subject, $configuration)->shouldReturn(true);
    }

    function it_should_recognize_subject_as_not_eligible_if_product_taxonomy_is_not_matched_and_exclude_is_set(
        OrderInterface $subject,
        OrderItemInterface $item,
        Taxon $taxon,
        ProductInterface $product,
        Collection $taxons
    ) {
        $configuration = array('taxons' => $taxons, 'exclude' => true);

        $taxons->contains(2)->willReturn(true);
        $taxon->getId()->willReturn(2);
        $product->getTaxons()->willReturn(array($taxon));
        $item->getProduct()->willReturn($product);
        $subject->getItems()->willReturn(array($item));

        $this->isEligible($subject, $configuration)->shouldReturn(false);
    }
}
