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

use Behat\Mink\Driver\Selenium2Driver;
use Behat\Mink\Element\NodeElement;
use Behat\Mink\Exception\ElementNotFoundException;
use Sylius\Behat\Behaviour\NamesIt;
use Sylius\Behat\Behaviour\SpecifiesItsCode;
use Sylius\Behat\Page\Admin\Crud\CreatePage as BaseCreatePage;
use Sylius\Behat\Service\AutocompleteHelper;
use Webmozart\Assert\Assert;

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
        $count = count($this->getCollectionItems('rules'));

        $this->getDocument()->clickLink('Add rule');

        $this->getDocument()->waitFor(5, function () use ($count) {
            return $count + 1 === count($this->getCollectionItems('rules'));
        });

        $this->selectRuleOption('Type', $ruleName);
    }

    /**
     * {@inheritdoc}
     */
    public function selectRuleOption($option, $value, $multiple = false)
    {
        $this->getLastCollectionItem('rules')->find('named', array('select', $option))->selectOption($value, $multiple);
    }

    /**
     * {@inheritdoc}
     */
    public function selectAutocompleteRuleOption($option, $value, $multiple = false)
    {
        $option = strtolower(str_replace(' ', '_', $option));

        $ruleAutocomplete = $this
            ->getLastCollectionItem('rules')
            ->find('css', sprintf('input[type="hidden"][name*="[%s]"]', $option))
            ->getParent()
        ;

        if ($multiple && is_array($value)) {
            AutocompleteHelper::chooseValues($this->getSession(), $ruleAutocomplete, $value);

            return;
        }

        AutocompleteHelper::chooseValue($this->getSession(), $ruleAutocomplete, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function fillRuleOption($option, $value)
    {
        $this->getLastCollectionItem('rules')->fillField($option, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function fillRuleOptionForChannel($channelName, $option, $value)
    {
        $lastAction = $this->getChannelConfigurationOfLastRule($channelName);
        $lastAction->fillField($option, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function addAction($actionName)
    {
        $count = count($this->getCollectionItems('actions'));

        $this->getDocument()->clickLink('Add action');

        $this->getDocument()->waitFor(5, function () use ($count) {
            return $count + 1 === count($this->getCollectionItems('actions'));
        });

        $this->selectActionOption('Type', $actionName);
    }

    /**
     * {@inheritdoc}
     */
    public function selectActionOption($option, $value, $multiple = false)
    {
        $this->getLastCollectionItem('actions')->find('named', array('select', $option))->selectOption($value, $multiple);
    }

    /**
     * {@inheritdoc}
     */
    public function fillActionOption($option, $value)
    {
        $this->getLastCollectionItem('actions')->fillField($option, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function fillActionOptionForChannel($channelName, $option, $value)
    {
        $lastAction = $this->getChannelConfigurationOfLastAction($channelName);
        $lastAction->fillField($option, $value);
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
    public function getValidationMessageForAction()
    {
        $actionForm = $this->getLastCollectionItem('actions');

        $foundElement = $actionForm->find('css', '.sylius-validation-error');
        if (null === $foundElement) {
            throw new ElementNotFoundException($this->getSession(), 'Tag', 'css', '.sylius-validation-error');
        }

        return $foundElement->getText();
    }

    /**
     * {@inheritdoc}
     */
    public function selectAutoCompleteFilterOption($option, $value, $multiple = false)
    {
        $option = strtolower(str_replace(' ', '_', $option));

        $filterAutocomplete = $this
            ->getLastCollectionItem('actions')
            ->find('css', sprintf('input[type="hidden"][name*="[%s_filter]"]', $option))
            ->getParent()
        ;

        if ($multiple && is_array($value)) {
            AutocompleteHelper::chooseValues($this->getSession(), $filterAutocomplete, $value);

            return;
        }

        AutocompleteHelper::chooseValue($this->getSession(), $filterAutocomplete, $value);
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefinedElements()
    {
        return [
            'actions' => '#sylius_promotion_actions',
            'code' => '#sylius_promotion_code',
            'ends_at' => '#sylius_promotion_endsAt',
            'minimum' => '#sylius_promotion_actions_0_configuration_WEB-US_filters_price_range_filter_min',
            'maximum' => '#sylius_promotion_actions_0_configuration_WEB-US_filters_price_range_filter_max',
            'name' => '#sylius_promotion_name',
            'rules' => '#sylius_promotion_rules',
            'starts_at' => '#sylius_promotion_startsAt',
        ];
    }

    /**
     * @param string $channelName
     *
     * @return NodeElement
     */
    private function getChannelConfigurationOfLastAction($channelName)
    {
        return $this
            ->getLastCollectionItem('actions')
            ->find('css', sprintf('[id$="configuration"] .field:contains("%s")', $channelName))
        ;
    }

    /**
     * @param string $channelName
     *
     * @return NodeElement
     */
    private function getChannelConfigurationOfLastRule($channelName)
    {
        return $this
            ->getLastCollectionItem('rules')
            ->find('css', sprintf('[id$="configuration"] .field:contains("%s")', $channelName))
        ;
    }

    /**
     * @param string $collection
     *
     * @return NodeElement
     */
    private function getLastCollectionItem($collection)
    {
        $items = $this->getCollectionItems($collection);

        Assert::notEmpty($items);

        return end($items);
    }

    /**
     * @param string $collection
     *
     * @return NodeElement[]
     */
    private function getCollectionItems($collection)
    {
        $items = $this->getElement($collection)->findAll('css', 'div[data-form-collection="item"]');

        Assert::isArray($items);

        return $items;
    }
}
