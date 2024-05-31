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
use Sylius\Behat\Page\Admin\Product\UpdateSimpleProductPageInterface;
use Sylius\Behat\Page\Admin\ProductVariant\UpdatePageInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Webmozart\Assert\Assert;

final class AccessingEditPageFromProductShowPageContext implements Context
{
    public function __construct(
        private UpdateSimpleProductPageInterface $updateSimpleProductPage,
        private UpdatePageInterface $updateVariantProductPage,
    ) {
    }

    /**
     * @Then I should be on :product product edit page
     */
    public function iShouldBeOnProductEditPage(ProductInterface $product): void
    {
        Assert::true($this->updateSimpleProductPage->isOpen(['id' => $product->getId()]));
    }

    /**
     * @Then I should be on :variant variant edit page
     */
    public function iShouldBeOnVariantEditPage(ProductVariantInterface $variant): void
    {
        Assert::true($this->updateVariantProductPage->isOpen(['productId' => $variant->getProduct()->getId(), 'id' => $variant->getId()]));
    }
}
