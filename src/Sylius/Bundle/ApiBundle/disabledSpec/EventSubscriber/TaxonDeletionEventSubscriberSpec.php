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

namespace spec\Sylius\Bundle\ApiBundle\EventSubscriber;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ApiBundle\Exception\TaxonCannotBeRemoved;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Core\Promotion\Checker\TaxonInPromotionRuleCheckerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

final class TaxonDeletionEventSubscriberSpec extends ObjectBehavior
{
    function let(
        ChannelRepositoryInterface $channelRepository,
        TaxonInPromotionRuleCheckerInterface $taxonInPromotionRuleChecker,
    ): void {
        $this->beConstructedWith($channelRepository, $taxonInPromotionRuleChecker);
    }

    function it_allows_to_remove_taxon_if_any_channel_has_not_it_as_a_menu_taxon(
        TaxonInterface $taxon,
        HttpKernelInterface $kernel,
        Request $request,
        ChannelRepositoryInterface $channelRepository,
    ): void {
        $request->getMethod()->willReturn(Request::METHOD_DELETE);
        $taxon->getCode()->willReturn('WATCHES');
        $channelRepository->findOneBy(['menuTaxon' => $taxon])->willReturn(null);

        $this->protectFromRemovingMenuTaxon(new ViewEvent(
            $kernel->getWrappedObject(),
            $request->getWrappedObject(),
            HttpKernelInterface::MASTER_REQUEST,
            $taxon->getWrappedObject(),
        ));
    }

    function it_does_nothing_after_writing_other_entity(
        HttpKernelInterface $kernel,
        Request $request,
    ): void {
        $request->getMethod()->willReturn(Request::METHOD_DELETE);

        $this->protectFromRemovingMenuTaxon(new ViewEvent(
            $kernel->getWrappedObject(),
            $request->getWrappedObject(),
            HttpKernelInterface::MASTER_REQUEST,
            new \stdClass(),
        ));
    }

    function it_throws_an_exception_if_a_subject_is_menu_taxon(
        TaxonInterface $taxon,
        HttpKernelInterface $kernel,
        Request $request,
        ChannelRepositoryInterface $channelRepository,
        ChannelInterface $channel,
    ): void {
        $request->getMethod()->willReturn(Request::METHOD_DELETE);
        $taxon->getCode()->willReturn('WATCHES');
        $channelRepository->findOneBy(['menuTaxon' => $taxon])->willReturn($channel);

        $this
            ->shouldThrow(\Exception::class)
            ->during('protectFromRemovingMenuTaxon', [new ViewEvent(
                $kernel->getWrappedObject(),
                $request->getWrappedObject(),
                HttpKernelInterface::MASTER_REQUEST,
                $taxon->getWrappedObject(),
            )])
        ;
    }

    function it_does_not_throw_exception_when_taxon_is_not_being_deleted(
        TaxonInPromotionRuleCheckerInterface $taxonInPromotionRuleChecker,
        TaxonInterface $taxon,
        Request $request,
        HttpKernelInterface $kernel,
    ): void {
        $request->getMethod()->willReturn(Request::METHOD_POST);

        $event = new ViewEvent(
            $kernel->getWrappedObject(),
            $request->getWrappedObject(),
            HttpKernelInterface::MAIN_REQUEST,
            $taxon->getWrappedObject(),
        );

        $taxonInPromotionRuleChecker->isInUse($taxon)->shouldNotBeCalled();

        $this->shouldNotThrow()->during('protectFromRemovingTaxonInUseByPromotionRule', [$event]);
    }

    function it_does_not_throw_exception_when_taxon_is_not_in_use_by_a_promotion_rule(
        TaxonInPromotionRuleCheckerInterface $taxonInPromotionRuleChecker,
        TaxonInterface $taxon,
        Request $request,
        HttpKernelInterface $kernel,
    ): void {
        $request->getMethod()->willReturn(Request::METHOD_POST);

        $event = new ViewEvent(
            $kernel->getWrappedObject(),
            $request->getWrappedObject(),
            HttpKernelInterface::MAIN_REQUEST,
            $taxon->getWrappedObject(),
        );

        $taxonInPromotionRuleChecker->isInUse($taxon)->willReturn(false);

        $this->shouldNotThrow()->during('protectFromRemovingTaxonInUseByPromotionRule', [$event]);
    }

    function it_throws_an_exception_when_trying_to_delete_taxon_that_is_in_use_by_a_promotion_rule(
        TaxonInPromotionRuleCheckerInterface $taxonInPromotionRuleChecker,
        TaxonInterface $taxon,
        Request $request,
        HttpKernelInterface $kernel,
    ): void {
        $request->getMethod()->willReturn(Request::METHOD_DELETE);

        $event = new ViewEvent(
            $kernel->getWrappedObject(),
            $request->getWrappedObject(),
            HttpKernelInterface::MAIN_REQUEST,
            $taxon->getWrappedObject(),
        );

        $taxonInPromotionRuleChecker->isInUse($taxon)->willReturn(true);

        $this
            ->shouldThrow(TaxonCannotBeRemoved::class)
            ->during('protectFromRemovingTaxonInUseByPromotionRule', [$event])
        ;
    }
}
