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

    public function filterByCode(string $code): void
    {
        $this->getElement('code_filter')->setValue($code);
    }

    public function filterByName(string $name): void
    {
        $this->getElement('name_filter')->setValue($name);
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'code_filter' => '#criteria_code_value',
            'name_filter' => '#criteria_name_value',
            'show_product_button' => '[data-test-resource-id="%variant_id%"] [data-test-show-action="Show product"]',
        ]);
    }
}
