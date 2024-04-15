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

namespace Sylius\Component\Core\Factory;

use Sylius\Component\Promotion\Model\PromotionRuleInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

/**
 * @template T of PromotionRuleInterface
 *
 * @extends FactoryInterface<T>
 */
interface PromotionRuleFactoryInterface extends FactoryInterface
{
    public function createCartQuantity(int $count): PromotionRuleInterface;

    public function createItemTotal(string $channelCode, int $amount): PromotionRuleInterface;

    public function createHasTaxon(array $taxons): PromotionRuleInterface;


    public function createNthOrder(int $nth): PromotionRuleInterface;

    public function createContainsProduct(string $productCode): PromotionRuleInterface;
}
