<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Factory;

use Sylius\Bundle\CoreBundle\Promotion\Checker\Rule\ContainsProductRuleChecker;
use Sylius\Bundle\CoreBundle\Promotion\Checker\Rule\NthOrderRuleChecker;
use Sylius\Bundle\CoreBundle\Promotion\Checker\Rule\HasTaxonRuleChecker;
use Sylius\Bundle\CoreBundle\Promotion\Checker\Rule\TotalOfItemsFromTaxonRuleChecker;
use Sylius\Bundle\PromotionBundle\Checker\Rule\CartQuantityRuleChecker;
use Sylius\Bundle\PromotionBundle\Checker\Rule\ItemTotalRuleChecker;
use Sylius\Component\Core\Factory\PromotionRuleFactoryInterface;
use Sylius\Component\Promotion\Model\PromotionRuleInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class PromotionRuleFactory implements PromotionRuleFactoryInterface
{
    /**
     * @var FactoryInterface
     */
    private $decoratedFactory;

    /**
     * @param FactoryInterface $decoratedFactory
     */
    public function __construct(FactoryInterface $decoratedFactory)
    {
        $this->decoratedFactory = $decoratedFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function createNew()
    {
        return $this->decoratedFactory->createNew();
    }

    /**
     * {@inheritdoc}
     */
    public function createCartQuantity($count)
    {
        /** @var PromotionRuleInterface $rule */
        $rule = $this->createNew();
        $rule->setType(CartQuantityRuleChecker::TYPE);
        $rule->setConfiguration(['count' => $count]);

        return $rule;
    }

    /**
     * {@inheritdoc}
     */
    public function createItemTotal($channelCode, $amount)
    {
        /** @var PromotionRuleInterface $rule */
        $rule = $this->createNew();
        $rule->setType(ItemTotalRuleChecker::TYPE);
        $rule->setConfiguration([$channelCode => ['amount' => $amount]]);

        return $rule;
    }

    /**
     * {@inheritdoc}
     */
    public function createHasTaxon(array $taxons)
    {
        /** @var PromotionRuleInterface $rule */
        $rule = $this->createNew();
        $rule->setType(HasTaxonRuleChecker::TYPE);
        $rule->setConfiguration(['taxons' => $taxons]);

        return $rule;
    }

    /**
     * {@inheritdoc}
     */
    public function createItemsFromTaxonTotal($channelCode, $taxonCode, $amount)
    {
        /** @var PromotionRuleInterface $rule */
        $rule = $this->createNew();
        $rule->setType(TotalOfItemsFromTaxonRuleChecker::TYPE);
        $rule->setConfiguration([$channelCode => ['taxon' => $taxonCode, 'amount' => $amount]]);

        return $rule;
    }

    /**
     * {@inheritdoc}
     */
    public function createNthOrder($nth)
    {
        /** @var PromotionRuleInterface $rule */
        $rule = $this->createNew();
        $rule->setType(NthOrderRuleChecker::TYPE);
        $rule->setConfiguration(['nth' => $nth]);

        return $rule;
    }

    /**
     * {@inheritdoc}
     */
    public function createContainsProduct($productCode)
    {
        /** @var PromotionRuleInterface $rule */
        $rule = $this->createNew();
        $rule->setType(ContainsProductRuleChecker::TYPE);
        $rule->setConfiguration(['product_code' => $productCode]);

        return $rule;
    }
}
