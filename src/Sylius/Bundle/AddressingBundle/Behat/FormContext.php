<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\AddressingBundle\Behat;

use Sylius\Bundle\ResourceBundle\Behat\FormContext as BaseFormContext;

/**
 * @author Arnaud Langlade <arn0d.dev@gmail.com>
 */
class FormContext extends BaseFormContext
{
    /**
     * @Given /^I remove all the provinces$/
     */
    public function iRemoveAProvince()
    {
        $items = count($this->getSession()->getPage()->findAll(
            'xpath',
            '//div[@id="sylius_country_provinces"]//div[@data-form-collection="item"]'
        ));

        while (0 !== $items) {
            $this->deleteCollectionItem($items);
            $items--;
        }
    }

    /**
     * @Given /^I fill in the (\d+)(st|nd|th) province with "([^""]*)"$/
     */
    public function fillProvinceName($position, $fake, $value)
    {
        $this->fillInField('sylius_country[provinces][' . ($position - 1) . '][name]', $value);
    }

    /**
     * @Given /^I select "([^""]*)" from the (\d+)(st|nd|th) country$/
     */
    public function fillCountryMember($value, $position, $fake)
    {
        $this->fillInField('sylius_country[members][' . ($position - 1) . '][country]', $value);
    }

    /**
     * @Given /^I remove the first country$/
     */
    public function iRemoveTheFirstCountryMember()
    {
        $this->deleteCollectionItem(1);
    }
}
