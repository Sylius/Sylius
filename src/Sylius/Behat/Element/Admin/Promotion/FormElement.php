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

namespace Sylius\Behat\Element\Admin\Promotion;

use Behat\Mink\Element\NodeElement;
use Behat\Mink\Exception\ElementNotFoundException;
use Behat\Mink\Session;
use Sylius\Behat\Element\Admin\Crud\FormElement as BaseFormElement;
use Sylius\Behat\Service\Helper\AutocompleteHelperInterface;
use Sylius\Behat\Service\TabsHelper;

final class FormElement extends BaseFormElement implements FormElementInterface
{
    public function __construct(
        Session $session,
        $minkParameters,
        private readonly AutocompleteHelperInterface $autocompleteHelper,
    ) {
        parent::__construct($session, $minkParameters);
    }

    public function getPriority(): int
    {
        return (int) $this->getElement('priority')->getValue();
    }

    public function setPriority(?int $priority): void
    {
        $this->getElement('priority')->setValue($priority);
    }

    public function setStartsAt(\DateTimeInterface $dateTime): void
    {
        $timestamp = $dateTime->getTimestamp();

        $this->getElement('starts_at_date')->setValue(date('Y-m-d', $timestamp));
        $this->getElement('starts_at_time')->setValue(date('H:i', $timestamp));
    }

    public function setEndsAt(\DateTimeInterface $dateTime): void
    {
        $timestamp = $dateTime->getTimestamp();

        $this->getElement('ends_at_date')->setValue(date('Y-m-d', $timestamp));
        $this->getElement('ends_at_time')->setValue(date('H:i', $timestamp));
    }

    public function setUsageLimit(int $limit): void
    {
        $this->getElement('usage_limit')->setValue($limit);
    }

    public function makeExclusive(): void
    {
        $this->getElement('exclusive')->check();
    }

    public function makeNotAppliesToDiscountedItem(): void
    {
        $this->getElement('applies_to_discounted')->uncheck();
    }

    public function makeCouponBased(): void
    {
        $this->getElement('coupon_based')->check();
    }

    public function checkChannel(string $name): void
    {
        $this->getElement('channels')->checkField($name);
    }

    public function setLabel(string $label, string $localeCode): void
    {
        $this->getElement('label', ['%locale_code%' => $localeCode])->setValue($label);
    }

    public function hasLabel(string $label, string $localeCode): bool
    {
        return $label === $this->getElement('label', ['%locale_code%' => $localeCode])->getValue();
    }

    public function addAction(string $type): void
    {
        $this->getElement('add_action_button', ['%type%' => $type])->press();
        $this->waitForFormUpdate();
    }

    public function removeLastAction(): void
    {
        $this->getLastAction()->find('css', 'button[data-test-delete]')->press();
        $this->waitForFormUpdate();
    }

    public function fillActionOption(string $option, string $value): void
    {
        $this->getLastAction()->fillField($option, $value);
    }

    public function fillActionOptionForChannel(string $channelCode, string $option, string $value): void
    {
        $lastAction = $this->getChannelConfigurationOfLastAction($channelCode);
        $lastAction->fillField($option, $value);
    }

    public function selectActionOption(string $option, string $value, bool $multiple = false): void
    {
        $this->getLastAction()->find('named', ['select', $option])->selectOption($value, $multiple);
    }

    public function addRule(string $type): void
    {
        $this->getElement('add_rule_button', ['%type%' => $type])->press();
        $this->waitForFormUpdate();
    }

    public function removeLastRule(): void
    {
        $this->getLastRule()->find('css', 'button[data-test-delete]')->press();
        $this->waitForFormUpdate();
    }

    public function selectRuleOption(string $option, string $value, bool $multiple = false): void
    {
        $this->getLastRule()->find('named', ['select', $option])->selectOption($value, $multiple);
    }

    public function fillRuleOption(string $option, string $value): void
    {
        $this->getLastRule()->fillField($option, $value);
    }

    public function fillRuleOptionForChannel(string $channelCode, string $option, string $value): void
    {
        $lastRule = $this->getChannelConfigurationOfLastRule($channelCode);
        $lastRule->fillField($option, $value);
    }

    public function selectAutocompleteRuleOptions(array $values, ?string $channelCode = null): void
    {
        $count = count($this->getElement('rules')->findAll('css', '[data-test-entry-row]'));
        $locator = $channelCode ?
            sprintf('#sylius_admin_promotion_rules_%d_configuration_%s select', $count - 1, $channelCode) :
            sprintf('#sylius_admin_promotion_rules_%d_configuration select', $count - 1)
        ;
        foreach ($values as $value) {
            $this->autocompleteHelper->selectByName(
                $this->getDriver(),
                $this->getLastRule()->find('css', $locator)->getXpath(),
                $value,
            );
        }

        $this->waitForFormUpdate();
    }

    public function selectAutocompleteActionFilterOptions(array $values, string $channelCode, string $filterType): void
    {
        $count = count($this->getElement('actions')->findAll('css', '[data-test-entry-row]'));
        $locator = sprintf('#sylius_admin_promotion_actions_%d_configuration_%s_filters_%s_filter select', $count - 1, $channelCode, $filterType);
        foreach ($values as $value) {
            $this->autocompleteHelper->selectByName(
                $this->getDriver(),
                $this->getLastAction()->find('css', $locator)->getXpath(),
                $value,
            );
        }

        $this->waitForFormUpdate();
    }

    public function checkIfRuleConfigurationFormIsVisible(): bool
    {
        return $this->hasElement('rule_count');
    }

    public function checkIfActionConfigurationFormIsVisible(): bool
    {
        return $this->hasElement('action_amount');
    }

    public function getValidationMessageForAction(): string
    {
        $actionForm = $this->getLastAction();

        $foundElement = $actionForm->find('css', '.invalid-feedback');
        if (null === $foundElement) {
            throw new ElementNotFoundException($this->getSession(), 'Tag', 'css', '.invalid-feedback');
        }

        return $foundElement->getText();
    }

    public function getValidationMessageForTranslation(string $element, string $localeCode): string
    {
        $foundElement = $this->getElement($element, ['%locale_code%' => $localeCode])->getParent();

        $validationMessage = $foundElement->find('css', '.invalid-feedback');
        if (null === $validationMessage) {
            throw new ElementNotFoundException($this->getSession(), 'Validation message', 'css', '.invalid-feedback');
        }

        return $validationMessage->getText();
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'action_amount' => '#sylius_admin_promotion_actions_0_configuration_WEB-US_amount',
            'actions' => '#sylius_admin_promotion_actions',
            'add_action_button' => '[data-test-actions] [data-test-add-%type%]',
            'add_rule_button' => '[data-test-rules] [data-test-add-%type%]',
            'applies_to_discounted' => '#sylius_admin_promotion_appliesToDiscounted',
            'channels' => '#sylius_admin_promotion_channels',
            'code' => '#sylius_admin_promotion_code',
            'coupon_based' => '#sylius_admin_promotion_couponBased',
            'ends_at_date' => '#sylius_admin_promotion_endsAt_date',
            'ends_at_time' => '#sylius_admin_promotion_endsAt_time',
            'exclusive' => '#sylius_admin_promotion_exclusive',
            'form' => '[data-live-name-value="sylius_admin:promotion:form"]',
            'label' => '[name="sylius_admin_promotion[translations][%locale_code%][label]"]',
            'last_action' => '[data-test-actions] [data-test-entry-row]:last-child',
            'last_rule' => '[data-test-rules] [data-test-entry-row]:last-child',
            'minimum' => '#sylius_admin_promotion_actions_0_configuration_WEB-US_filters_price_range_filter_min',
            'maximum' => '#sylius_admin_promotion_actions_0_configuration_WEB-US_filters_price_range_filter_max',
            'name' => '#sylius_admin_promotion_name',
            'priority' => '#sylius_admin_promotion_priority',
            'rule_count' => '#sylius_admin_promotion_rules_0_configuration_count',
            'rules' => '#sylius_admin_promotion_rules',
            'starts_at_date' => '#sylius_admin_promotion_startsAt_date',
            'starts_at_time' => '#sylius_admin_promotion_startsAt_time',
            'translation_tab' => '[data-test-promotion-translations-accordion="%locale_code%"]',
            'usage_limit' => '#sylius_admin_promotion_usageLimit',
        ]);
    }

    private function getLastAction(): NodeElement
    {
        return $this->getElement('last_action');
    }

    private function getChannelConfigurationOfLastAction(string $channelCode): NodeElement
    {
        $lastAction = $this->getLastAction();

        TabsHelper::switchTab($this->getSession(), $lastAction, $channelCode);

        return $lastAction
            ->find('css', sprintf('[id^="sylius_admin_promotion_actions_"][id$="_configuration_%s"]', $channelCode))
        ;
    }

    private function getLastRule(): NodeElement
    {
        return $this->getElement('last_rule');
    }

    private function getChannelConfigurationOfLastRule(string $channelCode): NodeElement
    {
        $lastRule = $this->getLastRule();

        TabsHelper::switchTab($this->getSession(), $lastRule, $channelCode);

        return $lastRule->find(
            'css',
            sprintf('[id^="sylius_admin_promotion_rules_"][id$="_configuration_%s"]', $channelCode),
        );
    }
}
