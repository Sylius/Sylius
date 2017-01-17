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

use Sylius\Behat\Behaviour\ChecksCodeImmutability;
use Sylius\Behat\Page\Admin\Crud\UpdatePage as BaseUpdatePage;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class UpdatePage extends BaseUpdatePage implements UpdatePageInterface
{
    use ChecksCodeImmutability;

    public function removeZone()
    {
        $this->getDocument()->selectFieldOption('Zone', 'Select');
    }

    /**
     * {@inheritdoc}
     */
    protected function getCodeElement()
    {
        return $this->getElement('code');
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
