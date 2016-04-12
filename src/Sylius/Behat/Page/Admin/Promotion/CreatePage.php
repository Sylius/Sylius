<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Page\Admin\Promotion;

use Behat\Mink\Element\NodeElement;
use Sylius\Behat\Behaviour\NamesIt;
use Sylius\Behat\Behaviour\SpecifiesItsCode;
use Sylius\Behat\Page\Admin\Crud\CreatePage as BaseCreatePage;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class CreatePage extends BaseCreatePage implements CreatePageInterface
{
    use NamesIt;
    use SpecifiesItsCode;

    /**
     * {@inheritdoc}
     */
    public function addRule($ruleName)
    {
        $this->getDocument()->clickLink('Add rule');

        $this->selectRuleOption('Type', $ruleName);
    }

    /**
     * {@inheritdoc}
     */
    public function selectRuleOption($option, $value, $multiple = false)
    {
        $this->getLastAddedRule()->find('named', array('select', $option))->selectOption($value, $multiple);
    }

    /**
     * {@inheritdoc}
     */
    public function fillRuleOption($option, $value)
    {
        $this->getLastAddedRule()->fillField($option, $value);
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefinedElements()
    {
        return [
            'code' => '#sylius_promotion_code',
            'name' => '#sylius_promotion_name',
            'rules' => '#sylius_promotion_rules',
        ];
    }

    /**
     * @return mixed
     */
    private function getLastAddedRule()
    {
        $rules = $this->getElement('rules')->findAll('css', 'div[data-form-collection="item"]');

        return end($rules);
    }
}
