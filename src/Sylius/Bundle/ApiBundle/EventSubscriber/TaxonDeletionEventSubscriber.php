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

namespace Sylius\Bundle\ApiBundle\EventSubscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use Sylius\Bundle\ApiBundle\Exception\CannotRemoveMenuTaxonException;
use Sylius\Bundle\ApiBundle\Exception\TaxonCannotBeRemoved;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Core\Promotion\Checker\TaxonInPromotionRuleCheckerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final class TaxonDeletionEventSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private ChannelRepositoryInterface $channelRepository,
        private TaxonInPromotionRuleCheckerInterface $taxonInPromotionRuleChecker,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::VIEW => [
                ['protectFromRemovingMenuTaxon', EventPriorities::PRE_WRITE],
                ['protectFromRemovingTaxonInUseByPromotionRule', EventPriorities::PRE_WRITE],
            ],
        ];
    }

    public function protectFromRemovingMenuTaxon(ViewEvent $event): void
    {
        $taxon = $event->getControllerResult();
        $method = $event->getRequest()->getMethod();

        if (!$taxon instanceof TaxonInterface || $method !== Request::METHOD_DELETE) {
            return;
        }

        $channel = $this->channelRepository->findOneBy(['menuTaxon' => $taxon]);

        if ($channel !== null) {
            throw new CannotRemoveMenuTaxonException($taxon->getCode());
        }
    }

    public function protectFromRemovingTaxonInUseByPromotionRule(ViewEvent $event): void
    {
        $taxon = $event->getControllerResult();
        $method = $event->getRequest()->getMethod();

        if (!$taxon instanceof TaxonInterface || $method !== Request::METHOD_DELETE) {
            return;
        }

        if ($this->taxonInPromotionRuleChecker->isInUse($taxon)) {
            throw new TaxonCannotBeRemoved('Cannot delete a taxon that is in use by a promotion rule.');
        }
    }
}
