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

namespace Sylius\Behat\Context\Ui\Admin;

use Behat\Behat\Context\Context;
use Sylius\Behat\Page\Admin\ProductVariant\UpdatePageInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;

final class ManagingProductVariantsPricesContext implements Context
{
    public function __construct(private UpdatePageInterface $updatePage)
    {
    }

    /**
     * @When /^I change the price of the ("[^"]+" product variant) to "(?:€|£|\$)([^"]+)" in ("[^"]+" channel)$/
     */
    public function iChangeThePriceOfTheProductVariantInChannel(
        ProductVariantInterface $variant,
        int $price,
        ChannelInterface $channel,
    ): void {
        $this->updatePage->open(['productId' => $variant->getProduct()->getId(), 'id' => $variant->getId()]);
        $this->updatePage->specifyPrice($price, $channel);
        $this->updatePage->saveChanges();
    }

    /**
     * @When /^I change the original price of the ("[^"]+" product variant) to "(?:€|£|\$)([^"]+)" in ("[^"]+" channel)$/
     */
    public function iChangeTheOriginalPriceOfTheProductVariantInChannel(
        ProductVariantInterface $variant,
        int $originalPrice,
        ChannelInterface $channel,
    ): void {
        $this->updatePage->open(['productId' => $variant->getProduct()->getId(), 'id' => $variant->getId()]);
        $this->updatePage->specifyOriginalPrice($originalPrice, $channel);
        $this->updatePage->saveChanges();
    }

    /**
     * @When /^I remove the original price of the ("[^"]+" product variant) in ("[^"]+" channel)$/
     */
    public function iRemoveTheOriginalPriceOfTheProductVariantInChannel(
        ProductVariantInterface $variant,
        ChannelInterface $channel,
    ): void {
        $this->updatePage->open(['productId' => $variant->getProduct()->getId(), 'id' => $variant->getId()]);
        $this->updatePage->specifyOriginalPrice(null, $channel);
        $this->updatePage->saveChanges();
    }
}
