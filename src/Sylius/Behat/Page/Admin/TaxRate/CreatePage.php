<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Page\Admin\TaxRate;

use Sylius\Behat\Behaviour\NamesIt;
use Sylius\Behat\Behaviour\SpecifiesItsCode;
use Sylius\Behat\Page\Admin\Crud\CreatePage as BaseCreatePage;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class CreatePage extends BaseCreatePage implements CreatePageInterface
{
    use NamesIt;
    use SpecifiesItsCode;

    /**
     * {@inheritdoc}
     */
    public function chooseZone($name)
    {
        $this->getDocument()->selectFieldOption('Zone', $name);
    }

    /**
     * {@inheritdoc}
     */
    public function chooseCategory($name)
    {
        $this->getDocument()->selectFieldOption('Category', $name);
    }

    /**
     * {@inheritdoc}
     */
    public function chooseCalculator($name)
    {
        $this->getDocument()->selectFieldOption(\Calculator::class, $name);
    }

    /**
     * {@inheritdoc}
     */
    public function specifyAmount($amount)
    {
        $this->getDocument()->fillField('Amount', $amount);
    }

    public function chooseIncludedInPrice()
    {
        $this->getDocument()->find('css', 'label[for=sylius_tax_rate_includedInPrice]')->click();
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefinedElements()
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
