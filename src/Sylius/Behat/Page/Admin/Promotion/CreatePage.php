<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Behat\Page\Admin\Promotion;

use Behat\Mink\Element\NodeElement;
use Behat\Mink\Exception\ElementNotFoundException;
use Sylius\Behat\Behaviour\NamesIt;
use Sylius\Behat\Behaviour\SpecifiesItsField;
use Sylius\Behat\Page\Admin\Crud\CreatePage as BaseCreatePage;
use Sylius\Behat\Service\AutocompleteHelper;
use Sylius\Behat\Service\TabsHelper;
use Webmozart\Assert\Assert;

class CreatePage extends BaseCreatePage implements CreatePageInterface
{
    use NamesIt;
    use SpecifiesItsField;

    public function specifyLabel(string $label, string $localeCode): void
    {
        $this->getDocument()->fillField(sprintf('sylius_promotion_translations_%s_label', $localeCode), $label);
    }

    public function addRule(?string $ruleName): void
    {
        $count = count($this->getCollectionItems('rules'));

        $this->getDocument()->clickLink('Add rule');

        $this->getDocument()->waitFor(5, fn () => $count + 1 === count($this->getCollectionItems('rules')));

        if (null !== $ruleName) {
            $this->selectRuleOption('Type', $ruleName);
        }
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

    public function fillRuleOptionForChannel(string $channelCode, string $option, string $value): void
    {
        $lastAction = $this->getChannelConfigurationOfLastRule($channelCode);
        $lastAction->fillField($option, $value);
    }

    public function addAction(?string $actionName): void
    {
        $count = count($this->getCollectionItems('actions'));

        $this->getDocument()->clickLink('Add action');

        $this->getDocument()->waitFor(5, fn () => $count + 1 === count($this->getCollectionItems('actions')));

        if (null !== $actionName) {
            $this->selectActionOption('Type', $actionName);
        }
    }

    public function selectActionOption(string $option, string $value, bool $multiple = false): void
    {
        $this->getLastCollectionItem('actions')->find('named', ['select', $option])->selectOption($value, $multiple);
    }

    public function fillActionOption(string $option, string $value): void
    {
        $this->getLastCollectionItem('actions')->fillField($option, $value);
    }

    public function fillActionOptionForChannel(string $channelCode, string $option, string $value): void
    {
        $lastAction = $this->getChannelConfigurationOfLastAction($channelCode);
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

    public function makeNotAppliesToDiscountedItem(): void
    {
        $this->getDocument()->unCheckField('Applies to already discounted order items');
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

    public function checkIfRuleConfigurationFormIsVisible(): bool
    {
        return $this->hasElement('count');
    }

    public function checkIfActionConfigurationFormIsVisible(): bool
    {
        return $this->hasElement('amount');
    }

    public function hasLabel(string $label, string $localeCode): bool
    {
        $labelElement = $this->getDocument()->find('css', sprintf('label:contains("%s")', $label));
        if (null === $labelElement) {
            return false;
        }

        return $labelElement->hasClass(sprintf('sylius-locale-%s', $localeCode));
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
            'count' => '#sylius_promotion_rules_0_configuration_count',
            'amount' => '#sylius_promotion_actions_0_configuration_WEB-US_amount',
        ];
    }

    private function getChannelConfigurationOfLastAction(string $channelCode): NodeElement
    {
        $lastAction = $this->getLastCollectionItem('actions');

        TabsHelper::switchTab($this->getSession(), $lastAction, $channelCode);

        return $lastAction
            ->find('css', sprintf('[id^="sylius_promotion_actions_"][id$="_configuration_%s"]', $channelCode))
        ;
    }

    private function getChannelConfigurationOfLastRule(string $channelCode): NodeElement
    {
        $lastRule = $this->getLastCollectionItem('rules');

        TabsHelper::switchTab($this->getSession(), $lastRule, $channelCode);

        return $lastRule
            ->find('css', sprintf('[id^="sylius_promotion_rules_"][id$="_configuration_%s"]', $channelCode))
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
