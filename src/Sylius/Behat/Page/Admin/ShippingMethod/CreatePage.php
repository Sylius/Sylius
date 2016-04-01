<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Page\Admin\ShippingMethod;

use Sylius\Behat\Behaviour\ChoosesCalculator;
use Sylius\Behat\Behaviour\SpecifiesItsAmount;
use Sylius\Behat\Behaviour\SpecifiesItsCode;
use Sylius\Behat\Page\Admin\Crud\CreatePage as BaseCreatePage;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class CreatePage extends BaseCreatePage implements CreatePageInterface
{
    use SpecifiesItsCode;

    /**
     * {@inheritdoc}
     */
    public function nameIt($name, $language)
    {
        $this->getDocument()->fillField(sprintf('sylius_shipping_method_translations_%s_name', $language), $name);
    }

    /**
     * {@inheritdoc}
     */
    public function specifyAmount($amount)
    {
        $this->getDocument()->fillField('Amount', $amount);
    }

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
    public function chooseCalculator($name)
    {        
        $this->getDocument()->selectFieldOption('Calculator', $name);
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefinedElements()
    {
        return array_merge(parent::getDefinedElements(), [
            'amount' => '#sylius_shipping_method_configuration_amount',
            'calculator' => '#sylius_shipping_method_calculator',
            'code' => '#sylius_shipping_method_code',
            'name' => '#sylius_shipping_method_translations_en_US_name',
            'zone' => '#sylius_shipping_method_zone',
        ]);
    }
}
