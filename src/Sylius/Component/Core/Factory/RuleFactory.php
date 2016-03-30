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

use Sylius\Component\Core\Promotion\Checker\TotalOfItemsFromTaxonRuleChecker;
use Sylius\Component\Core\Promotion\Checker\ContainsTaxonRuleChecker;
use Sylius\Component\Core\Promotion\Checker\TaxonRuleChecker;
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
        $rule->setType(RuleInterface::TYPE_CART_QUANTITY);
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
        $rule->setType(RuleInterface::TYPE_ITEM_TOTAL);
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
}
