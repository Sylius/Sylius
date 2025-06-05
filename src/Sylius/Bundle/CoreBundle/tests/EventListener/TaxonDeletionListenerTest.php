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

namespace Tests\Sylius\Bundle\CoreBundle\EventListener;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\CoreBundle\EventListener\TaxonDeletionListener;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Core\Promotion\Checker\TaxonInPromotionRuleCheckerInterface;
use Sylius\Component\Core\Promotion\Updater\Rule\TaxonAwareRuleUpdaterInterface;
use Sylius\Resource\Symfony\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

final class TaxonDeletionListenerTest extends TestCase
{
    private MockObject&RequestStack $requestStack;

    private ChannelRepositoryInterface&MockObject $channelRepository;

    private MockObject&TaxonInPromotionRuleCheckerInterface $taxonInPromotionRuleChecker;

    private MockObject&TaxonAwareRuleUpdaterInterface $hasTaxonRuleUpdater;

    private MockObject&TaxonAwareRuleUpdaterInterface $taxonAwareRuleUpdater;

    private TaxonDeletionListener $taxonDeletionListener;

    protected function setUp(): void
    {
        $this->requestStack = $this->createMock(RequestStack::class);
        $this->channelRepository = $this->createMock(ChannelRepositoryInterface::class);
        $this->taxonInPromotionRuleChecker = $this->createMock(TaxonInPromotionRuleCheckerInterface::class);
        $this->hasTaxonRuleUpdater = $this->createMock(TaxonAwareRuleUpdaterInterface::class);
        $this->taxonAwareRuleUpdater = $this->createMock(TaxonAwareRuleUpdaterInterface::class);
        $this->taxonDeletionListener = new TaxonDeletionListener(
            $this->requestStack,
            $this->channelRepository,
            $this->taxonInPromotionRuleChecker,
            $this->hasTaxonRuleUpdater,
            $this->taxonAwareRuleUpdater,
        );
    }

    public function testThrowsExceptionWhenSubjectIsNotTaxon(): void
    {
        $event = $this->createMock(GenericEvent::class);
        $event->expects(self::once())->method('getSubject')->willReturn('subject');

        $this->expectException(\InvalidArgumentException::class);

        $this->taxonDeletionListener->protectFromRemovingTaxonInUseByPromotionRule($event);
    }

    public function testDoesNotAllowToRemoveTaxonIfAnyChannelHasItAsMenuTaxon(): void
    {
        $event = $this->createMock(GenericEvent::class);
        $taxon = $this->createMock(TaxonInterface::class);
        $channel = $this->createMock(ChannelInterface::class);
        $session = $this->createMock(SessionInterface::class);
        $flashBag = $this->createMock(FlashBagInterface::class);

        $event->expects(self::once())->method('getSubject')->willReturn($taxon);

        $this->channelRepository
            ->expects(self::once())
            ->method('findOneBy')
            ->with(['menuTaxon' => $taxon])
            ->willReturn($channel)
        ;

        $this->requestStack->expects(self::once())->method('getSession')->willReturn($session);
        $session->expects(self::once())->method('getBag')->with('flashes')->willReturn($flashBag);
        $flashBag->expects(self::once())->method('add')->with('error', 'sylius.taxon.menu_taxon_delete');
        $event->expects(self::once())->method('stopPropagation');

        $this->taxonDeletionListener->protectFromRemovingMenuTaxon($event);
    }

    public function testDoesNothingIfTaxonIsNotMenuTaxonOfAnyChannel(): void
    {
        $event = $this->createMock(GenericEvent::class);
        $taxon = $this->createMock(TaxonInterface::class);

        $event->expects(self::once())->method('getSubject')->willReturn($taxon);

        $this->channelRepository
            ->expects(self::once())
            ->method('findOneBy')
            ->with(['menuTaxon' => $taxon])
            ->willReturn(null)
        ;

        $this->requestStack->expects(self::never())->method('getSession');

        $this->taxonDeletionListener->protectFromRemovingMenuTaxon($event);
    }

    public function testThrowsExceptionIfEventSubjectIsNotTaxonInProtectFromRemovingMenuTaxon(): void
    {
        $event = $this->createMock(GenericEvent::class);
        $event->expects(self::once())->method('getSubject')->willReturn('wrongSubject');

        $this->expectException(\InvalidArgumentException::class);

        $this->taxonDeletionListener->protectFromRemovingMenuTaxon($event);
    }

    public function testAddsFlashThatPromotionsHaveBeenUpdated(): void
    {
        $event = $this->createMock(GenericEvent::class);
        $taxon = $this->createMock(TaxonInterface::class);
        $session = $this->createMock(SessionInterface::class);
        $flashBag = $this->createMock(FlashBagInterface::class);

        $event->expects(self::once())->method('getSubject')->willReturn($taxon);

        $this->hasTaxonRuleUpdater
            ->expects(self::once())
            ->method('updateAfterDeletingTaxon')
            ->with($taxon)
            ->willReturn(['christmas', 'holiday'])
        ;

        $this->taxonAwareRuleUpdater
            ->expects(self::once())
            ->method('updateAfterDeletingTaxon')
            ->with($taxon)
            ->willReturn(['christmas'])
        ;

        $this->requestStack->expects(self::once())->method('getSession')->willReturn($session);
        $session->expects(self::once())->method('getBag')->with('flashes')->willReturn($flashBag);

        $flashBag->expects(self::once())
            ->method('add')
            ->with(
                'info',
                [
                    'message' => 'sylius.promotion.update_rules',
                    'parameters' => ['%codes%' => 'christmas, holiday'],
                ],
            )
        ;

        $this->taxonDeletionListener->removeTaxonFromPromotionRules($event);
    }

    public function testDoesNothingIfNoPromotionHasBeenUpdated(): void
    {
        $event = $this->createMock(GenericEvent::class);
        $taxon = $this->createMock(TaxonInterface::class);

        $event->expects(self::once())->method('getSubject')->willReturn($taxon);

        $this->hasTaxonRuleUpdater
            ->expects(self::once())
            ->method('updateAfterDeletingTaxon')
            ->with($taxon)
            ->willReturn([])
        ;

        $this->taxonAwareRuleUpdater
            ->expects(self::once())
            ->method('updateAfterDeletingTaxon')
            ->with($taxon)
            ->willReturn([])
        ;

        $this->requestStack->expects(self::never())->method('getSession');

        $this->taxonDeletionListener->removeTaxonFromPromotionRules($event);
    }

    public function testChangesTaxonPositionToMinusOneIfBasePositionIsZero(): void
    {
        $event = $this->createMock(GenericEvent::class);
        $taxon = $this->createMock(TaxonInterface::class);

        $event->expects(self::once())->method('getSubject')->willReturn($taxon);
        $taxon->expects(self::once())->method('getPosition')->willReturn(0);
        $taxon->expects(self::once())->method('setPosition')->with(-1);

        $this->taxonDeletionListener->handleRemovingRootTaxonAtPositionZero($event);
    }

    public function testDoesNothingWhenProductIsNotAssignedToRule(): void
    {
        $event = $this->createMock(GenericEvent::class);
        $taxon = $this->createMock(TaxonInterface::class);

        $event->expects(self::once())->method('getSubject')->willReturn($taxon);

        $this->taxonInPromotionRuleChecker
            ->expects(self::once())
            ->method('isInUse')
            ->with($taxon)
            ->willReturn(false)
        ;

        $event->expects(self::never())->method('setMessageType');
        $event->expects(self::never())->method('setMessage');
        $event->expects(self::never())->method('stopPropagation');

        $this->taxonDeletionListener->protectFromRemovingTaxonInUseByPromotionRule($event);
    }

    public function testPreventsToRemoveProductIfItIsAssignedToRule(): void
    {
        $event = $this->createMock(GenericEvent::class);
        $taxon = $this->createMock(TaxonInterface::class);

        $event->expects(self::once())->method('getSubject')->willReturn($taxon);

        $this->taxonInPromotionRuleChecker
            ->expects(self::once())
            ->method('isInUse')
            ->with($taxon)
            ->willReturn(true)
        ;

        $event->expects(self::once())->method('setMessageType')->with('error');
        $event->expects(self::once())->method('setMessage')->with('sylius.taxon.in_use_by_promotion_rule');
        $event->expects(self::once())->method('stopPropagation');

        $this->taxonDeletionListener->protectFromRemovingTaxonInUseByPromotionRule($event);
    }
}
