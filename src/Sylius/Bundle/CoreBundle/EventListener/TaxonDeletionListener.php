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
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Webmozart\Assert\Assert;

final class TaxonDeletionListener
{
    /** @var TaxonAwareRuleUpdaterInterface[] */
    private array $ruleUpdaters;

    public function __construct(
        private SessionInterface|RequestStack $requestStackOrSession,
        private ChannelRepositoryInterface $channelRepository,
        TaxonAwareRuleUpdaterInterface ...$ruleUpdaters
    ) {
        $this->ruleUpdaters = $ruleUpdaters;

        if ($requestStackOrSession instanceof SessionInterface) {
            trigger_deprecation('sylius/user-bundle', '2.0', sprintf('Passing an instance of %s as constructor argument for %s is deprecated as of Sylius 1.12 and will be removed in 2.0. Pass an instance of %s instead.', SessionInterface::class, self::class, RequestStack::class));
        }
    }

    public function protectFromRemovingMenuTaxon(GenericEvent $event): void
    {
        $taxon = $event->getSubject();
        Assert::isInstanceOf($taxon, TaxonInterface::class);

        $channel = $this->channelRepository->findOneBy(['menuTaxon' => $taxon]);
        if ($channel !== null) {
            if ($this->requestStackOrSession instanceof SessionInterface) {
                $session = $this->requestStackOrSession;
            } else {
                $session = $this->requestStackOrSession->getSession();
            }

            /** @var FlashBagInterface $flashes */
            $flashes = $session->getBag('flashes');
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
            if ($this->requestStackOrSession instanceof SessionInterface) {
                $session = $this->requestStackOrSession;
            } else {
                $session = $this->requestStackOrSession->getSession();
            }

            /** @var FlashBagInterface $flashes */
            $flashes = $session->getBag('flashes');
            $flashes->add('info', [
                'message' => 'sylius.promotion.update_rules',
                'parameters' => ['%codes%' => implode(', ', array_unique($updatedPromotionCodes))],
            ]);
        }
    }
}
