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

use Sylius\Behat\Page\Admin\Crud\CreatePage as BaseCreatePage;
use Webmozart\Assert\Assert;

class CreatePage extends BaseCreatePage implements CreatePageInterface
{
    public function selectCountry(string $countryName): void
    {
        $this->getElement('code')->selectOption($countryName);
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

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'code' => '[data-test-code]',
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
}
