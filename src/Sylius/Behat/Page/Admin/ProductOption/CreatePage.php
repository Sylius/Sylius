<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Behat\Page\Admin\ProductOption;

use Behat\Mink\Element\NodeElement;
use Behat\Mink\Exception\ElementNotFoundException;
use Sylius\Behat\Behaviour\SpecifiesItsCode;
use Sylius\Behat\Page\Admin\Crud\CreatePage as BaseCreatePage;
use Webmozart\Assert\Assert;

class CreatePage extends BaseCreatePage implements CreatePageInterface
{
    use SpecifiesItsCode;

    public function nameItIn(string $name, string $language): void
    {
        $this->getDocument()->fillField(
            sprintf('sylius_product_option_translations_%s_name', $language), $name
        );
    }

    public function addOptionValue(string $code, string $value): void
    {
        $this->getDocument()->clickLink('Add value');

        $optionValueForm = $this->getLastOptionValueElement();

        $optionValueForm->fillField('Code', $code);
        $optionValueForm->fillField('English (United States)', $value);
    }

    public function checkValidationMessageForOptionValues(string $message): bool
    {
        $optionValuesValidationElement = $this->getElement('values_validation')->find('css', '.sylius-validation-error');
        if (null === $optionValuesValidationElement) {
            throw new ElementNotFoundException($this->getDriver(), 'product option validation box', 'css', '.sylius-validation-error');
        }

        return $optionValuesValidationElement->getText() === $message;
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'code' => '#sylius_product_option_code',
            'name' => '#sylius_product_option_translations_en_US_name',
            'values' => '#sylius_product_option_values',
            'values_validation' => '.ui.segment',
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
