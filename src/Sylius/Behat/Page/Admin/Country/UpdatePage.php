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

    public function addProvince(): void
    {
        $count = count($this->getProvinceItems());

        $this->getElement('add_province')->click();

        $this->getDocument()->waitFor(5, fn () => $count + 1 === count($this->getProvinceItems()));
    }

    public function specifyProvinceName(string $name): void
    {
        $province = $this->getElement('last_province');
        $province->find('css', '[data-test-province-name]')->setValue($name);
    }

    public function specifyProvinceCode(string $code): void
    {
        $province = $this->getElement('last_province');
        $province->find('css', '[data-test-province-code]')->setValue($code);
    }

    public function specifyProvinceAbbreviation(string $abbreviation): void
    {
        $province = $this->getElement('last_province');
        $province->find('css', '[data-test-province-abbreviation]')->setValue($abbreviation);
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
            $province = $this->getProvinceElement($provinceName);

            $province->find('css', '[data-test-delete-province]')->click();
            $this->getDocument()->waitFor(5, fn () => !$this->isThereProvince($provinceName));
        }
    }

    public function removeProvinceName(string $provinceName): void
    {
        if ($this->isThereProvince($provinceName)) {
            $province = $this->getProvinceElement($provinceName);
            $province->find('css', '[data-test-province-name]')->setValue('');
        }
    }

    public function getFormValidationErrors(): array
    {
        $errors = $this->getElement('form')->findAll('css', '.alert-danger');

        return array_map(fn (NodeElement $element) => $element->getText(), $errors);
    }

    public function getValidationMessage(string $element): string
    {
        $province = $this->getElement('last_province');

        $foundElement = $province->find('css', '.invalid-feedback');
        if (null === $foundElement) {
            throw new ElementNotFoundException($this->getSession(), 'Tag', 'css', '.invalid-feedback');
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
            'code' => '[data-test-code]',
            'enabled' => '[data-test-enabled]',
            'provinces' => '[data-test-provinces]',
            'last_province' => '[data-test-provinces] [data-test-province]:last-child',
            'add_province' => '[data-test-add-province]',
        ]);
    }

    private function getProvinceItems(): array
    {
        $items = $this->getElement('provinces')->findAll('css', '[data-test-province]');
        Assert::isArray($items);

        return $items;
    }

    private function getProvinceElement(string $provinceName): NodeElement|null
    {
        return $this->getDocument()->find('xpath', sprintf('//*[@data-test-province and .//*[contains(@value, \'%s\')]]', $provinceName));
    }
}
