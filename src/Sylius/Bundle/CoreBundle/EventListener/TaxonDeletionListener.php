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

namespace Sylius\Bundle\CoreBundle\EventListener;

use Sylius\Bundle\CoreBundle\Provider\FlashBagProvider;
use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Core\Promotion\Checker\TaxonInPromotionRuleCheckerInterface;
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
        private RequestStack|SessionInterface $requestStackOrSession,
        private ChannelRepositoryInterface $channelRepository,
        private TaxonInPromotionRuleCheckerInterface $taxonInPromotionRuleChecker,
        TaxonAwareRuleUpdaterInterface ...$ruleUpdaters,
    ) {
        $this->ruleUpdaters = $ruleUpdaters;

        if ($requestStackOrSession instanceof SessionInterface) {
            trigger_deprecation(
                'sylius/user-bundle',
                '1.12',
                'Passing an instance of %s as constructor argument for %s is deprecated and will be removed in Sylius 2.0. Pass an instance of %s instead.',
                SessionInterface::class,
                self::class,
                RequestStack::class,
            );
        }
    }

    public function protectFromRemovingMenuTaxon(GenericEvent $event): void
    {
        $taxon = $event->getSubject();
        Assert::isInstanceOf($taxon, TaxonInterface::class);

        $channel = $this->channelRepository->findOneBy(['menuTaxon' => $taxon]);
        if ($channel !== null) {
            /** @var FlashBagInterface $flashes */
            $flashes = FlashBagProvider::getFlashBag($this->requestStackOrSession);
            $flashes->add('error', 'sylius.taxon.menu_taxon_delete');

            $event->stopPropagation();
        }
    }

    public function protectFromRemovingTaxonInUseByPromotionRule(ResourceControllerEvent $event): void
    {
        $taxon = $event->getSubject();
        Assert::isInstanceOf($taxon, TaxonInterface::class);

        if ($this->taxonInPromotionRuleChecker->isInUse($taxon)) {
            $event->setMessageType('error');
            $event->setMessage('sylius.taxon.in_use_by_promotion_rule');
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
            $flashes = FlashBagProvider::getFlashBag($this->requestStackOrSession);
            $flashes->add('info', [
                'message' => 'sylius.promotion.update_rules',
                'parameters' => ['%codes%' => implode(', ', array_unique($updatedPromotionCodes))],
            ]);
        }
    }

    public function handleRemovingRootTaxonAtPositionZero(GenericEvent $event): void
    {
        /** @var TaxonInterface $taxon */
        $taxon = $event->getSubject();
        Assert::isInstanceOf($taxon, TaxonInterface::class);

        if ($taxon->getPosition() === 0) {
            $taxon->setPosition(-1);
        }
    }
}
