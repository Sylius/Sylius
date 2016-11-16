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

use Sylius\Component\Promotion\Model\PromotionRuleInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
interface PromotionRuleFactoryInterface extends FactoryInterface
{
    /**
     * @param int $count
     *
     * @return PromotionRuleInterface
     */
    public function createCartQuantity($count);

    /**
     * @param int $amount
     *
     * @return PromotionRuleInterface
     */
    public function createItemTotal($amount);

    /**
     * @param array $taxons
     *
     * @return PromotionRuleInterface
     */
    public function createHasTaxon(array $taxons);

    /**
     * @param string $taxonCode
     * @param int $amount
     *
     * @return PromotionRuleInterface
     */
    public function createItemsFromTaxonTotal($taxonCode, $amount);

    /**
     * @param int $nth
     *
     * @return PromotionRuleInterface
     */
    public function createNthOrder($nth);

    /**
     * @param string $productCode
     *
     * @return PromotionRuleInterface
     */
    public function createContainsProduct($productCode);
}
