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
use Sylius\Behat\Behaviour\ChoosesName;
use Sylius\Behat\Page\Admin\Crud\CreatePage as BaseCreatePage;
use Webmozart\Assert\Assert;

class CreatePage extends BaseCreatePage implements CreatePageInterface
{
    use ChoosesName;

    public function addProvince(string $name, string $code, string $abbreviation = null): void
    {
        $this->getElement('add_province')->click();

        $this->waitForElement(5, 'last_province');
        $province = $this->getElement('last_province');

        $province->find('css', '[data-test-province-name]')->setValue($name);
        $province->find('css', '[data-test-province-code]')->setValue($code);

        if (null !== $abbreviation) {
            $province->find('css', '[data-test-province-abbreviation]')->setValue($abbreviation);
        }
    }

    public function selectCountry(string $countryName): void
    {
        $this->getElement('code')->selectOption($countryName);
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'code' => '[data-test-code]',
            'last_province' => '[data-test-provinces] [data-test-province]:last-child',
            'add_province' => '[data-test-add-province]'
        ]);
    }

    private function waitForElement(int $timeout, string $elementName): bool
    {
        return $this->getDocument()->waitFor($timeout, fn () => $this->hasElement($elementName));
    }
}
