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

namespace Sylius\Bundle\CoreBundle\EventListener;

use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Core\Promotion\Updater\Rule\TaxonAwareRuleUpdaterInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Webmozart\Assert\Assert;

final class TaxonDeletionListener
{
    /** @var SessionInterface */
    private $session;

    /** @var ChannelRepositoryInterface */
    private $channelRepository;

    /** @var TaxonAwareRuleUpdaterInterface[] */
    private $ruleUpdaters;

    public function __construct(
        SessionInterface $session,
        ChannelRepositoryInterface $channelRepository,
        TaxonAwareRuleUpdaterInterface ...$ruleUpdaters
    ) {
        $this->session = $session;
        $this->channelRepository = $channelRepository;
        $this->ruleUpdaters = $ruleUpdaters;
    }

    public function protectFromRemovingMenuTaxon(GenericEvent $event): void
    {
        $taxon = $event->getSubject();
        Assert::isInstanceOf($taxon, TaxonInterface::class);

        $channel = $this->channelRepository->findOneBy(['menuTaxon' => $taxon]);
        if ($channel !== null) {
            /** @var FlashBagInterface $flashes */
            $flashes = $this->session->getBag('flashes');
            $flashes->add('error', 'sylius.taxon.menu_taxon_delete');

            $event->stopPropagation();
        }
    }

    public function removeTaxonFromPromotionRules(GenericEvent $event): void
    {
        $taxon = $event->getSubject();
        Assert::isInstanceOf($taxon, TaxonInterface::class);

        $updatedPromotionCodes = [];
        foreach ($this->ruleUpdaters as $ruleUpdater) {
            $updatedPromotionCodes = array_merge($updatedPromotionCodes, $ruleUpdater->updateAfterDeletingTaxon($taxon));
        }

        if (!empty($updatedPromotionCodes)) {
            /** @var FlashBagInterface $flashes */
            $flashes = $this->session->getBag('flashes');
            $flashes->add('info', [
                'message' => 'sylius.promotion.update_rules',
                'parameters' => ['%codes%' => implode(', ', array_unique($updatedPromotionCodes))],
            ]);
        }
    }
}
