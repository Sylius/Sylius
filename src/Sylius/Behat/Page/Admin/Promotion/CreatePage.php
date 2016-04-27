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
 * @author Łuksaz Zalewski <mateusz.zalewski@lakion.com>
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
        $this->getLastAddedCollectionItem('rules')->find('named', array('select', $option))->selectOption($value, $multiple);
    }

    /**
     * {@inheritdoc}
     */
    public function fillRuleOption($option, $value)
    {
        $this->getLastAddedCollectionItem('rules')->fillField($option, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function addAction($actionName)
    {
        $this->getDocument()->clickLink('Add action');

        $this->selectActionOption('Type', $actionName);
    }

    /**
     * {@inheritdoc}
     */
    public function selectActionOption($option, $value, $multiple = false)
    {
        $this->getLastAddedCollectionItem('actions')->find('named', array('select', $option))->selectOption($value, $multiple);
    }

    /**
     * {@inheritdoc}
     */
    public function fillActionOption($option, $value)
    {
        $this->getLastAddedCollectionItem('actions')->fillField($option, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function fillUsageLimit($limit)
    {
        $this->getDocument()->fillField('Usage limit', $limit);
    }

    public function makeExclusive()
    {
        $this->getDocument()->checkField('Exclusive');
    }

    public function checkCouponBased()
    {
        $this->getDocument()->checkField('Coupon based');
    }

    public function checkChannel($name)
    {
        $this->getDocument()->checkField($name);
    }

    /**
     * {@inheritdoc}
     */
    public function setStartsAt(\DateTime $dateTime)
    {
        $timestamp = $dateTime->getTimestamp();

        $this->getDocument()->fillField('sylius_promotion_startsAt_date', date('Y-m-d', $timestamp));
        $this->getDocument()->fillField('sylius_promotion_startsAt_time', date('H:i', $timestamp));
    }

    /**
     * {@inheritdoc}
     */
    public function setEndsAt(\DateTime $dateTime)
    {
        $timestamp = $dateTime->getTimestamp();

        $this->getDocument()->fillField('sylius_promotion_endsAt_date', date('Y-m-d', $timestamp));
        $this->getDocument()->fillField('sylius_promotion_endsAt_time', date('H:i', $timestamp));
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefinedElements()
    {
        return [
            'starts_at' => '#sylius_promotion_startsAt',
            'ends_at' => '#sylius_promotion_endsAt',
            'actions' => '#sylius_promotion_actions',
            'code' => '#sylius_promotion_code',
            'name' => '#sylius_promotion_name',
            'rules' => '#sylius_promotion_rules',
        ];
    }

    /**
     * @param string $collection
     *
     * @return NodeElement
     */
    private function getLastAddedCollectionItem($collection)
    {
        $rules = $this->getElement($collection)->findAll('css', 'div[data-form-collection="item"]');

        return end($rules);
    }
}
