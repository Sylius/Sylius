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

namespace Sylius\Behat\Page\Admin\CatalogPromotion\ProductVariant;

use Sylius\Behat\Page\Admin\Crud\IndexPage as BaseIndexPage;

class IndexPage extends BaseIndexPage implements IndexPageInterface
{
    public function showProductOf(int $variantId): void
    {
        $this->getElement('show_product_button', ['%variant_id%' => $variantId])->click();
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'show_product_button' => '[data-test-resource-id="%variant_id%"] [data-test-show-action="Show product"]',
        ]);
    }
}
