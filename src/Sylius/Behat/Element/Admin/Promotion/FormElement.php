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
use FriendsOfBehat\PageObjectExtension\Element\Element;
use Sylius\Behat\Service\Helper\AutocompleteHelperInterface;
use Sylius\Behat\Service\TabsHelper;
use Webmozart\Assert\Assert;

final class FormElement extends Element implements FormElementInterface
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
        $this->getElement('translation_tab', ['%locale_code%' => $localeCode])->press();
        $this->getElement('label', ['%locale_code%' => $localeCode])->setValue($label);
    }

    public function hasLabel(string $label, string $localeCode): bool
    {
        return $label === $this->getElement('label', ['%locale_code%' => $localeCode])->getValue();
    }

    public function addAction(?string $actionName): void
    {
        $this->getElement('add_action_button')->press();
        $this->waitForFormUpdate();

        if (null !== $actionName) {
            $this->selectActionOption('Type', $actionName);
            $this->waitForFormUpdate();
        }
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

    public function addRule(?string $ruleName): void
    {
        $this->getElement('add_rule_button')->press();
        $this->waitForFormUpdate();

        if (null !== $ruleName) {
            $this->selectRuleOption('Type', $ruleName);
            $this->waitForFormUpdate();
        }
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
        $count = count($this->getElement('rules')->findAll('css', '[data-test-promotion-rule]'));
        $locator = $channelCode ?
            sprintf('#sylius_promotion_rules_%d_configuration_%s select', $count - 1, $channelCode) :
            sprintf('#sylius_promotion_rules_%d_configuration select', $count - 1)
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

    public function selectAutocompleteFilterOptions(array $values, string $channelCode, string $filterType): void
    {
        $count = count($this->getElement('actions')->findAll('css', '[data-test-promotion-action]'));
        $locator = sprintf('#sylius_promotion_actions_%d_configuration_%s_filters_%s_filter select', $count - 1, $channelCode, $filterType);
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

    public function getValidationMessage(string $element): string
    {
        $foundElement = $this->getFieldElement($element);
        if (null === $foundElement) {
            throw new ElementNotFoundException($this->getSession(), 'Field element');
        }

        $validationMessage = $foundElement->find('css', '.invalid-feedback');
        if (null === $validationMessage) {
            throw new ElementNotFoundException($this->getSession(), 'Validation message', 'css', '.invalid-feedback');
        }

        return $validationMessage->getText();
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
        $this->getElement('translation_tab', ['%locale_code%' => $localeCode])->press();
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
            'action_amount' => '#sylius_promotion_actions_0_configuration_WEB-US_amount',
            'actions' => '#sylius_promotion_actions',
            'add_action_button' => '#sylius_promotion_actions_add',
            'add_rule_button' => '#sylius_promotion_rules_add',
            'applies_to_discounted' => '#sylius_promotion_appliesToDiscounted',
            'channels' => '#sylius_promotion_channels',
            'code' => '#sylius_promotion_code',
            'coupon_based' => '#sylius_promotion_couponBased',
            'ends_at_date' => '#sylius_promotion_endsAt_date',
            'ends_at_time' => '#sylius_promotion_endsAt_time',
            'exclusive' => '#sylius_promotion_exclusive',
            'form' => '[data-live-name-value="SyliusAdmin.Promotion.Form"]',
            'label' => '[name="sylius_promotion[translations][%locale_code%][label]"]',
            'minimum' => '#sylius_promotion_actions_0_configuration_WEB-US_filters_price_range_filter_min',
            'maximum' => '#sylius_promotion_actions_0_configuration_WEB-US_filters_price_range_filter_max',
            'name' => '#sylius_promotion_name',
            'priority' => '#sylius_promotion_priority',
            'rule_count' => '#sylius_promotion_rules_0_configuration_count',
            'rules' => '#sylius_promotion_rules',
            'starts_at_date' => '#sylius_promotion_startsAt_date',
            'starts_at_time' => '#sylius_promotion_startsAt_time',
            'translation_tab' => '[data-test-promotion-translations-accordion="%locale_code%"]',
            'usage_limit' => '#sylius_promotion_usageLimit',
        ]);
    }

    private function getLastAction(): NodeElement
    {
        $items = $this->getElement('actions')->findAll('css', '[data-test-promotion-action]');
        Assert::notEmpty($items);

        return end($items);
    }

    private function getChannelConfigurationOfLastAction(string $channelCode): NodeElement
    {
        $lastAction = $this->getLastAction();

        TabsHelper::switchTab($this->getSession(), $lastAction, $channelCode);

        return $lastAction
            ->find('css', sprintf('[id^="sylius_promotion_actions_"][id$="_configuration_%s"]', $channelCode))
        ;
    }

    private function getLastRule(): NodeElement
    {
        $items = $this->getElement('rules')->findAll('css', '[data-test-promotion-rule]');
        Assert::notEmpty($items);

        return end($items);
    }

    private function getChannelConfigurationOfLastRule(string $channelCode): NodeElement
    {
        $lastRule = $this->getLastRule();

        TabsHelper::switchTab($this->getSession(), $lastRule, $channelCode);

        return $lastRule
            ->find('css', sprintf('[id^="sylius_promotion_rules_"][id$="_configuration_%s"]', $channelCode))
        ;
    }

    /** @throws ElementNotFoundException */
    private function getFieldElement(string $element): ?NodeElement
    {
        $element = $this->getElement($element);
        while (null !== $element && !$element->hasClass('field')) {
            $element = $element->getParent();
        }

        return $element;
    }

    private function waitForFormUpdate(): void
    {
        $form = $this->getElement('form');
        sleep(1); // we need to sleep, as sometimes the check below is executed faster than the form sets the busy attribute
        $form->waitFor(1500, function () use ($form) {
            return !$form->hasAttribute('busy');
        });
    }
}
