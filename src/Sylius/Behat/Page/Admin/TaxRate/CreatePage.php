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

namespace Sylius\Behat\Page\Admin\TaxRate;

use Sylius\Behat\Behaviour\NamesIt;
use Sylius\Behat\Behaviour\SpecifiesItsCode;
use Sylius\Behat\Page\Admin\Crud\CreatePage as BaseCreatePage;

class CreatePage extends BaseCreatePage implements CreatePageInterface
{
    use NamesIt;
    use SpecifiesItsCode;

    public function chooseZone(string $name): void
    {
        $this->getDocument()->selectFieldOption('Zone', $name);
    }

    public function chooseCategory(string $name): void
    {
        $this->getDocument()->selectFieldOption('Category', $name);
    }

    public function chooseCalculator(string $name): void
    {
        $this->getDocument()->selectFieldOption('Calculator', $name);
    }

    public function specifyAmount(string $amount): void
    {
        $this->getDocument()->fillField('Amount', $amount);
    }

    public function chooseIncludedInPrice(): void
    {
        $this->getDocument()->find('css', 'label[for=sylius_tax_rate_includedInPrice]')->click();
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'amount' => '#sylius_tax_rate_amount',
            'calculator' => '#sylius_tax_rate_calculator',
            'category' => '#sylius_tax_rate_category',
            'code' => '#sylius_tax_rate_code',
            'name' => '#sylius_tax_rate_name',
            'zone' => '#sylius_tax_rate_zone',
        ]);
    }
}
