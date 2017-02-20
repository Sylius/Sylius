<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Page\Admin\ProductAttribute;

use Sylius\Behat\Behaviour\SpecifiesItsCode;
use Sylius\Behat\Page\Admin\Crud\CreatePage as BaseCreatePage;

/**
 * @author Anna Walasek <anna.walasek@lakion.com>
 */
class CreatePage extends BaseCreatePage implements CreatePageInterface
{
    use SpecifiesItsCode;

    /**
     * @var int
     */
    private $choiceListIndex = 0;

    /**
     * {@inheritdoc}
     */
    public function nameIt($name, $language)
    {
        $this->getDocument()->fillField(sprintf('sylius_product_attribute_translations_%s_name', $language), $name);
    }

    /**
     * {@inheritdoc}
     */
    public function isTypeDisabled()
    {
        return 'disabled' === $this->getElement('type')->getAttribute('disabled');
    }

    /**
     * {@inheritdoc}
     */
    public function addAttributeValue($value)
    {
        $this->getDocument()->clickLink('Add');
        $this->getElement('attribute_choice_list_element', ['%index%' => $this->choiceListIndex])->setValue($value);
        $this->choiceListIndex++;
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefinedElements()
    {
        return array_merge(parent::getDefinedElements(), [
            'attribute_choice_list' => 'div[data-form-collection="list"]',
            'attribute_choice_list_element' => '#sylius_product_attribute_configuration_choices_%index%',
            'code' => '#sylius_product_attribute_code',
            'name' => '#sylius_product_attribute_translations_en_US_name',
            'type' => '#sylius_product_attribute_type',
        ]);
    }
}
