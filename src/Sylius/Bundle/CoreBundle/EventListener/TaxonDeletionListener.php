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

use Doctrine\ORM\EntityManagerInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Core\Promotion\Checker\Rule\HasTaxonRuleChecker;
use Sylius\Component\Core\Promotion\Checker\Rule\TotalOfItemsFromTaxonRuleChecker;
use Sylius\Component\Promotion\Model\PromotionRuleInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Webmozart\Assert\Assert;

final class TaxonDeletionListener
{
    /** @var RepositoryInterface */
    private $promotionRuleRepository;

    /** @var EntityManagerInterface */
    private $manager;

    public function __construct(RepositoryInterface $promotionRuleRepository, EntityManagerInterface $manager)
    {
        $this->promotionRuleRepository = $promotionRuleRepository;
        $this->manager = $manager;
    }

    public function removeTaxonFromPromotionRules(GenericEvent $event): void
    {
        $taxon = $event->getSubject();
        Assert::isInstanceOf($taxon, TaxonInterface::class);

        $this->resolveHasTaxonRules($taxon->getCode());
        $this->resolveTotalOfItemsFromTaxonRules($taxon->getCode());

        $this->manager->flush();
    }

    private function resolveHasTaxonRules(string $taxonCode): void
    {
        $promotionRules = $this->promotionRuleRepository->findBy(['type' => HasTaxonRuleChecker::TYPE]);

        /** @var PromotionRuleInterface $promotionRule */
        foreach ($promotionRules as $promotionRule) {
            $configuration = $promotionRule->getConfiguration();
            if (in_array($taxonCode, $configuration['taxons'])) {
                $configuration['taxons'] = array_values(array_diff($configuration['taxons'], [$taxonCode]));
                $promotionRule->setConfiguration($configuration);
            }
        }
    }

    private function resolveTotalOfItemsFromTaxonRules(string $taxonCode): void
    {
        $promotionRules = $this->promotionRuleRepository->findBy(['type' => TotalOfItemsFromTaxonRuleChecker::TYPE]);

        /** @var PromotionRuleInterface $promotionRule */
        foreach ($promotionRules as $promotionRule) {
            $this->removePromotionRuleIfNecessary($promotionRule, $taxonCode);
        }
    }

    private function removePromotionRuleIfNecessary(PromotionRuleInterface $promotionRule, string $taxonCode): void
    {
        foreach ($promotionRule->getConfiguration() as $configuration) {
            if ($taxonCode === $configuration['taxon']) {
                $this->promotionRuleRepository->remove($promotionRule);

                return;
            }
        }
    }
}
