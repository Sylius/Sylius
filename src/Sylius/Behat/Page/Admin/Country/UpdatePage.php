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

namespace Sylius\Behat\Page\Admin\Country;

use Behat\Mink\Element\NodeElement;
use Behat\Mink\Exception\ElementNotFoundException;
use Sylius\Behat\Behaviour\Toggles;
use Sylius\Behat\Page\Admin\Crud\UpdatePage as BaseUpdatePage;
use Webmozart\Assert\Assert;

class UpdatePage extends BaseUpdatePage implements UpdatePageInterface
{
    use Toggles;

    public function isCodeFieldDisabled(): bool
    {
        $codeField = $this->getElement('code');

        return $codeField->getAttribute('disabled') === 'disabled';
    }

    public function isThereProvince(string $provinceName): bool
    {
        $provinces = $this->getElement('provinces');

        return $provinces->has('css', '[value = "' . $provinceName . '"]');
    }

    public function isThereProvinceWithCode(string $provinceCode): bool
    {
        $provinces = $this->getElement('provinces');

        return $provinces->has('css', '[value = "' . $provinceCode . '"]');
    }

    public function removeProvince(string $provinceName): void
    {
        if ($this->isThereProvince($provinceName)) {
            $provinces = $this->getElement('provinces');

            $item = $provinces
                ->find('css', sprintf('div[data-form-collection="item"] input[value="%s"]', $provinceName))
                ->getParent()
                ->getParent()
                ->getParent()
                ->getParent()
                ->getParent()
            ;

            $item->clickLink('Delete');
        }
    }

    public function addProvince(string $name, string $code, ?string $abbreviation = null): void
    {
        $this->clickAddProvinceButton();

        $provinceForm = $this->getLastProvinceElement();

        $provinceForm->fillField('Name', $name);
        $provinceForm->fillField('Code', $code);

        if (null !== $abbreviation) {
            $provinceForm->fillField('Abbreviation', $abbreviation);
        }
    }

    public function clickAddProvinceButton(): void
    {
        $this->getDocument()->clickLink('Add province');
    }

    public function nameProvince(string $name): void
    {
        $provinceForm = $this->getLastProvinceElement();

        $provinceForm->fillField('Name', $name);
    }

    public function removeProvinceName(string $provinceName): void
    {
        if ($this->isThereProvince($provinceName)) {
            $provinces = $this->getElement('provinces');

            $item = $provinces->find('css', 'div[data-form-collection="item"] input[value="' . $provinceName . '"]')->getParent();
            $item->fillField('Name', '');
        }
    }

    public function specifyProvinceCode(string $code): void
    {
        $provinceForm = $this->getLastProvinceElement();

        $provinceForm->fillField('Code', $code);
    }

    public function getFormValidationErrors(): array
    {
        $errors = $this->getElement('form')->findAll('css', '.sylius-validation-error:not(.pointing)');

        return array_map(fn (NodeElement $element) => $element->getText(), $errors);
    }

    public function getValidationMessage(string $element): string
    {
        $provinceForm = $this->getLastProvinceElement();

        $foundElement = $provinceForm->find('css', '.sylius-validation-error');
        if (null === $foundElement) {
            throw new ElementNotFoundException($this->getSession(), 'Tag', 'css', '.sylius-validation-error');
        }

        return $foundElement->getText();
    }

    protected function getToggleableElement(): NodeElement
    {
        return $this->getElement('enabled');
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'code' => '#sylius_country_code',
            'enabled' => '#sylius_country_enabled',
            'form' => 'form',
            'provinces' => '#sylius_country_provinces',
        ]);
    }

    private function getLastProvinceElement(): NodeElement
    {
        $provinces = $this->getElement('provinces');
        $items = $provinces->findAll('css', 'div[data-form-collection="item"]');

        Assert::notEmpty($items);

        return end($items);
    }
}
