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

namespace Sylius\Behat\Page\Admin\Promotion;

use Behat\Mink\Element\NodeElement;
use Behat\Mink\Exception\ElementNotFoundException;
use Sylius\Behat\Behaviour\NamesIt;
use Sylius\Behat\Behaviour\SpecifiesItsCode;
use Sylius\Behat\Page\Admin\Crud\CreatePage as BaseCreatePage;
use Sylius\Behat\Service\AutocompleteHelper;
use Webmozart\Assert\Assert;

class CreatePage extends BaseCreatePage implements CreatePageInterface
{
    use NamesIt;
    use SpecifiesItsCode;

    public function addRule(string $ruleName): void
    {
        $count = count($this->getCollectionItems('rules'));

        $this->getDocument()->clickLink('Add rule');

        $this->getDocument()->waitFor(5, function () use ($count) {
            return $count + 1 === count($this->getCollectionItems('rules'));
        });

        $this->selectRuleOption('Type', $ruleName);
    }

    public function selectRuleOption(string $option, string $value, bool $multiple = false): void
    {
        $this->getLastCollectionItem('rules')->find('named', ['select', $option])->selectOption($value, $multiple);
    }

    public function selectAutocompleteRuleOption(string $option, $value, bool $multiple = false): void
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

    public function fillRuleOption(string $option, string $value): void
    {
        $this->getLastCollectionItem('rules')->fillField($option, $value);
    }

    public function fillRuleOptionForChannel(string $channelName, string $option, string $value): void
    {
        $lastAction = $this->getChannelConfigurationOfLastRule($channelName);
        $lastAction->fillField($option, $value);
    }

    public function addAction(string $actionName): void
    {
        $count = count($this->getCollectionItems('actions'));

        $this->getDocument()->clickLink('Add action');

        $this->getDocument()->waitFor(5, function () use ($count) {
            return $count + 1 === count($this->getCollectionItems('actions'));
        });

        $this->selectActionOption('Type', $actionName);
    }

    public function selectActionOption(string $option, string $value, bool $multiple = false): void
    {
        $this->getLastCollectionItem('actions')->find('named', ['select', $option])->selectOption($value, $multiple);
    }

    public function fillActionOption(string $option, string $value): void
    {
        $this->getLastCollectionItem('actions')->fillField($option, $value);
    }

    public function fillActionOptionForChannel(string $channelName, string $option, string $value): void
    {
        $lastAction = $this->getChannelConfigurationOfLastAction($channelName);
        $lastAction->fillField($option, $value);
    }

    public function fillUsageLimit(string $limit): void
    {
        $this->getDocument()->fillField('Usage limit', $limit);
    }

    public function makeExclusive(): void
    {
        $this->getDocument()->checkField('Exclusive');
    }

    public function checkCouponBased(): void
    {
        $this->getDocument()->checkField('Coupon based');
    }

    public function checkChannel(string $name): void
    {
        $this->getDocument()->checkField($name);
    }

    public function setStartsAt(\DateTimeInterface $dateTime): void
    {
        $timestamp = $dateTime->getTimestamp();

        $this->getDocument()->fillField('sylius_promotion_startsAt_date', date('Y-m-d', $timestamp));
        $this->getDocument()->fillField('sylius_promotion_startsAt_time', date('H:i', $timestamp));
    }

    public function setEndsAt(\DateTimeInterface $dateTime): void
    {
        $timestamp = $dateTime->getTimestamp();

        $this->getDocument()->fillField('sylius_promotion_endsAt_date', date('Y-m-d', $timestamp));
        $this->getDocument()->fillField('sylius_promotion_endsAt_time', date('H:i', $timestamp));
    }

    public function getValidationMessageForAction(): string
    {
        $actionForm = $this->getLastCollectionItem('actions');

        $foundElement = $actionForm->find('css', '.sylius-validation-error');
        if (null === $foundElement) {
            throw new ElementNotFoundException($this->getSession(), 'Tag', 'css', '.sylius-validation-error');
        }

        return $foundElement->getText();
    }

    public function selectAutoCompleteFilterOption(string $option, $value, bool $multiple = false): void
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

    protected function getDefinedElements(): array
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

    private function getChannelConfigurationOfLastAction(string $channelName): NodeElement
    {
        return $this
            ->getLastCollectionItem('actions')
            ->find('css', sprintf('[id$="configuration"] .field:contains("%s")', $channelName))
        ;
    }

    private function getChannelConfigurationOfLastRule(string $channelName): NodeElement
    {
        return $this
            ->getLastCollectionItem('rules')
            ->find('css', sprintf('[id$="configuration"] .field:contains("%s")', $channelName))
        ;
    }

    private function getLastCollectionItem(string $collection): NodeElement
    {
        $items = $this->getCollectionItems($collection);

        Assert::notEmpty($items);

        return end($items);
    }

    /**
     * @return NodeElement[]
     */
    private function getCollectionItems(string $collection): array
    {
        $items = $this->getElement($collection)->findAll('css', 'div[data-form-collection="item"]');

        Assert::isArray($items);

        return $items;
    }
}
