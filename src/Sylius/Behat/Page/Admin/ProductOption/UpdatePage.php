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

namespace Sylius\Behat\Page\Admin\ProductOption;

use Behat\Mink\Element\NodeElement;
use Sylius\Behat\Behaviour\ChecksCodeImmutability;
use Sylius\Behat\Page\Admin\Crud\UpdatePage as BaseUpdatePage;
use Webmozart\Assert\Assert;

class UpdatePage extends BaseUpdatePage implements UpdatePageInterface
{
    use ChecksCodeImmutability;

    public function nameItIn(string $name, string $language): void
    {
        $this->getDocument()->fillField(
            sprintf('sylius_product_option_translations_%s_name', $language),
            $name,
        );
    }

    public function isThereOptionValue(string $optionValue): bool
    {
        $optionValues = $this->getElement('values');

        return $optionValues->has('css', sprintf('input[value="%s"]', $optionValue));
    }

    public function addOptionValue(string $code, string $value): void
    {
        $this->getDocument()->clickLink('Add value');

        $optionValueForm = $this->getLastOptionValueElement();

        $optionValueForm->fillField('Code', $code);
        $optionValueForm->fillField('English (United States)', $value);
    }

    public function removeOptionValue(string $optionValue): void
    {
        if ($this->isThereOptionValue($optionValue)) {
            $optionValues = $this->getElement('values');

            $item = $optionValues
                ->find('css', sprintf('div[data-form-collection="item"] input[value="%s"]', $optionValue))
                ->getParent()
                ->getParent()
                ->getParent()
                ->getParent()
            ;
            $item->clickLink('Delete');
        }
    }

    protected function getCodeElement(): NodeElement
    {
        return $this->getElement('code');
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'code' => '#sylius_product_option_code',
            'name' => '#sylius_product_option_translations_en_US_name',
            'values' => '#sylius_product_option_values',
        ]);
    }

    private function getLastOptionValueElement(): NodeElement
    {
        $optionValues = $this->getElement('values');
        $items = $optionValues->findAll('css', 'div[data-form-collection="item"]');

        Assert::notEmpty($items);

        return end($items);
    }
}
