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
use Sylius\Behat\Page\Admin\Product\IndexPageInterface;
use Sylius\Behat\Service\SharedStorageInterface;
use Sylius\Component\Core\Model\ProductInterface;

final class RemovingProductContext implements Context
{
    public function __construct(
        private SharedStorageInterface $sharedStorage,
        private IndexPageInterface $indexPage,
    ) {
    }

    /**
     * @When I delete the :product product
     * @When I try to delete the :product product
     */
    public function iDeleteProduct(ProductInterface $product): void
    {
        $this->sharedStorage->set('product', $product);

        $this->indexPage->open();
        $this->indexPage->deleteResourceOnPage(['name' => $product->getName()]);
    }

    /**
     * @When I delete the :product product on filtered page
     */
    public function iDeleteProductOnFilteredPage(ProductInterface $product): void
    {
        $this->sharedStorage->set('product', $product);

        $this->indexPage->deleteResourceOnPage(['name' => $product->getName()]);
    }
}
