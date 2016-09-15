<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\Factory;

use Sylius\Component\Core\Promotion\Checker\Rule\ContainsTaxonRuleChecker;
use Sylius\Component\Core\Promotion\Checker\Rule\NthOrderRuleChecker;
use Sylius\Component\Core\Promotion\Checker\Rule\TaxonRuleChecker;
use Sylius\Component\Core\Promotion\Checker\Rule\TotalOfItemsFromTaxonRuleChecker;
use Sylius\Component\Promotion\Checker\Rule\CartQuantityRuleChecker;
use Sylius\Component\Promotion\Checker\Rule\ItemTotalRuleChecker;
use Sylius\Component\Promotion\Model\RuleInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class RuleFactory implements RuleFactoryInterface
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
        /** @var RuleInterface $rule */
        $rule = $this->createNew();
        $rule->setType(CartQuantityRuleChecker::TYPE);
        $rule->setConfiguration(['count' => $count]);

        return $rule;
    }

    /**
     * {@inheritdoc}
     */
    public function createItemTotal($amount)
    {
        /** @var RuleInterface $rule */
        $rule = $this->createNew();
        $rule->setType(ItemTotalRuleChecker::TYPE);
        $rule->setConfiguration(['amount' => $amount]);

        return $rule;
    }

    /**
     * {@inheritdoc}
     */
    public function createTaxon(array $taxons)
    {
        /** @var RuleInterface $rule */
        $rule = $this->createNew();
        $rule->setType(TaxonRuleChecker::TYPE);
        $rule->setConfiguration(['taxons' => $taxons]);

        return $rule;
    }

    /**
     * {@inheritdoc}
     */
    public function createItemsFromTaxonTotal($taxon, $amount)
    {
        /** @var RuleInterface $rule */
        $rule = $this->createNew();
        $rule->setType(TotalOfItemsFromTaxonRuleChecker::TYPE);
        $rule->setConfiguration(['taxon' => $taxon, 'amount' => $amount]);

        return $rule;
    }

    /**
     * {@inheritdoc}
     */
    public function createContainsTaxon($taxon, $count)
    {
        /** @var RuleInterface $rule */
        $rule = $this->createNew();
        $rule->setType(ContainsTaxonRuleChecker::TYPE);
        $rule->setConfiguration(['taxon' => $taxon, 'count' => $count]);

        return $rule;
    }

    /**
     * {@inheritdoc}
     */
    public function createNthOrder($nth)
    {
        /** @var RuleInterface $rule */
        $rule = $this->createNew();
        $rule->setType(NthOrderRuleChecker::TYPE);
        $rule->setConfiguration(['nth' => $nth]);

        return $rule;
    }
}
