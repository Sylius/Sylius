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

    protected $elements = [
        'code' => '#sylius_promotion_code',
        'name' => '#sylius_promotion_name',
        'rules' => '#sylius_promotion_rules',
    ];

    private $rulesCount = 0;

    /**
     * {@inheritdoc}
     */
    public function addRule($ruleName)
    {
        $this->getDocument()->clickLink('Add rule');

        $rules = $this->getElement('rules');

        $rules->selectFieldOption('sylius_promotion_rules_'.$this->rulesCount.'_type', $ruleName);
    }

    /**
     * {@inheritdoc}
     */
    public function selectRuleOption($option, $value, $multiple = false)
    {
        $rules = $this->getElement('rules');

        $rules
            ->selectFieldOption(
                'sylius_promotion_rules_'.$this->rulesCount.'_configuration_'.$option,
                $value,
                $multiple
            )
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function fillRuleOption($option, $value)
    {
        $rules = $this->getElement('rules');

        $rules->fillField('sylius_promotion_rules_'.$this->rulesCount.'_configuration_'.$option, $value);
    }
}
