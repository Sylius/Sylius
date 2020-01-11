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

namespace Sylius\Component\Core\Factory;

use Sylius\Component\Core\Promotion\Checker\Rule\ContainsProductRuleChecker;
use Sylius\Component\Core\Promotion\Checker\Rule\HasTaxonRuleChecker;
use Sylius\Component\Core\Promotion\Checker\Rule\NthOrderRuleChecker;
use Sylius\Component\Core\Promotion\Checker\Rule\TotalOfItemsFromTaxonRuleChecker;
use Sylius\Component\Promotion\Checker\Rule\CartQuantityRuleChecker;
use Sylius\Component\Promotion\Checker\Rule\ItemTotalRuleChecker;
use Sylius\Component\Promotion\Model\PromotionRuleInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

final class PromotionRuleFactory implements PromotionRuleFactoryInterface
{
    /** @var FactoryInterface */
    private $decoratedFactory;

    public function __construct(FactoryInterface $decoratedFactory)
    {
        $this->decoratedFactory = $decoratedFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function createNew(): PromotionRuleInterface
    {
        return $this->decoratedFactory->createNew();
    }

    /**
     * {@inheritdoc}
     */
    public function createCartQuantity(int $count): PromotionRuleInterface
    {
        return $this->createPromotionRule(CartQuantityRuleChecker::TYPE, ['count' => $count]);
    }

    /**
     * {@inheritdoc}
     */
    public function createItemTotal(string $channelCode, int $amount): PromotionRuleInterface
    {
        return $this->createPromotionRule(ItemTotalRuleChecker::TYPE, [$channelCode => ['amount' => $amount]]);
    }

    /**
     * {@inheritdoc}
     */
    public function createHasTaxon(array $taxons): PromotionRuleInterface
    {
        return $this->createPromotionRule(HasTaxonRuleChecker::TYPE, ['taxons' => $taxons]);
    }

    /**
     * {@inheritdoc}
     */
    public function createItemsFromTaxonTotal(string $channelCode, string $taxonCode, int $amount): PromotionRuleInterface
    {
        return $this->createPromotionRule(
            TotalOfItemsFromTaxonRuleChecker::TYPE,
            [$channelCode => ['taxon' => $taxonCode, 'amount' => $amount]])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function createNthOrder(int $nth): PromotionRuleInterface
    {
        return $this->createPromotionRule(NthOrderRuleChecker::TYPE, ['nth' => $nth]);
    }

    /**
     * {@inheritdoc}
     */
    public function createContainsProduct(string $productCode): PromotionRuleInterface
    {
        return $this->createPromotionRule(ContainsProductRuleChecker::TYPE, ['product_code' => $productCode]);
    }

    private function createPromotionRule(string $type, array $configuration): PromotionRuleInterface
    {
        /** @var PromotionRuleInterface $rule */
        $rule = $this->createNew();
        $rule->setType($type);
        $rule->setConfiguration($configuration);

        return $rule;
    }
}
