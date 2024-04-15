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

namespace Sylius\Behat\Element\Admin\ShippingMethod;

use Behat\Mink\Element\NodeElement;
use Behat\Mink\Exception\ElementNotFoundException;
use FriendsOfBehat\PageObjectExtension\Element\Element;
use Sylius\Behat\Service\DriverHelper;

final class FormElement extends Element implements FormElementInterface
{
    /**
     * @return array<string, string>
     */
    protected function getDefinedElements(): array
    {
        return [
            'calculator' => '#sylius_shipping_method_calculator',
            'calculator_configuration_amount' => '#sylius_shipping_method_configuration_%channelCode%_amount',
            'calculator_configuration_channel_tab' => '[data-test-calculator-configuration] [data-test-channel-tab="%channelCode%"]',
            'calculator_configuration_channel_tab_content' => '[data-test-calculator-configuration] [data-test-channel-tab-content="%channelCode%"]',
            'channel' => '[name="sylius_shipping_method[channels][]"][value="%channelCode%"]',
            'code' => '#sylius_shipping_method_code',
            'description' => '#sylius_shipping_method_translations_%localeCode%_description',
            'live_component' => '[data-controller="live"]',
            'name' => '#sylius_shipping_method_translations_%localeCode%_name',
            'position' => '#sylius_shipping_method_position',
            'rules_wrapper' => '#sylius_shipping_method_rules',
            'rule_configuration_amount' => '#sylius_shipping_method_rules_%position%_configuration_%channelCode%_amount',
            'rule_configuration_weight' => '#sylius_shipping_method_rules_%position%_configuration_weight',
            'shipping_method_rule_add_button' => '#sylius_shipping_method_rules_add',
            'zone' => '#sylius_shipping_method_zone',
        ];
    }

    public function getCode(): string
    {
        return $this->getElement('code')->getValue();
    }

    public function setCode(string $code): void
    {
        $this->getElement('code')->setValue($code);
    }

    public function isCodeDisabled(): bool
    {
        return $this->getElement('code')->hasAttribute('disabled');
    }

    public function getName(string $localeCode = 'en_US')
    {
        return $this->getElement('name', ['%localeCode%' => $localeCode])->getValue();
    }

    public function setName(string $name, string $localeCode = 'en_US'): void
    {
        $this->getElement('name', ['%localeCode%' => $localeCode])->setValue($name);
    }

    public function getPosition(): int
    {
        return (int) $this->getElement('position')->getValue();
    }

    public function setPosition(int $position): void
    {
        $this->getElement('position')->setValue($position);
    }

    public function getDescription(string $localeCode = 'en_US'): string
    {
        return $this->getElement('description', ['%localeCode%' => $localeCode])->getValue();
    }

    public function setDescription(string $description, string $localeCode = 'en_US'): void
    {
        $this->getElement('description', ['%localeCode%' => $localeCode])->setValue($description);
    }

    public function getZoneCode(): string
    {
        return $this->getElement('zone')->getValue();
    }

    public function setZoneCode(string $code): void
    {
        $this->getElement('zone')->setValue($code);
    }

    public function checkChannel(string $channelCode): void
    {
        $this->getElement('channel', ['%channelCode%' => $channelCode])->check();
    }

    public function hasCheckedChannel(string $channelCode): bool
    {
        return $this->getElement('channel', ['%channelCode%' => $channelCode])->isChecked();
    }

    public function setCalculatorConfigurationAmountForChannel(string $channelCode, int $amount): void
    {
        $this->selectCalculatorConfigurationChannelTab($channelCode);

        $this->getElement('calculator_configuration_amount', ['%channelCode%' => $channelCode])->setValue($amount);
    }

    public function chooseCalculator(string $calculatorName): void
    {
        $this->getElement('calculator')->selectOption($calculatorName);
        $this->waitForLiveComponentUpdate();
    }

    public function addRule(string $ruleName): void
    {
        $this->getElement('shipping_method_rule_add_button')->click();
        $this->waitForLiveComponentUpdate();

        $rules = $this->getElement('rules_wrapper')->findAll('css', 'div[data-test-rule]');
        /** @var NodeElement $lastRule */
        $lastRule = end($rules);
        $lastRule->selectFieldOption('Type', $ruleName);
        $this->waitForLiveComponentUpdate();
    }

    public function fillLastRuleOption(string $fieldName, string $value): void
    {
        $rules = $this->getElement('rules_wrapper')->findAll('css', 'div[data-test-rule]');
        /** @var NodeElement $lastRule */
        $lastRule = end($rules);

        $lastRule->fillField($fieldName, $value);

        $this->waitForLiveComponentUpdate();
    }

    public function fillLastRuleOptionForChannel(string $channelCode, string $fieldName, string $value): void
    {
        $rules = $this->getElement('rules_wrapper')->findAll('css', 'div[data-test-rule]');
        /** @var NodeElement $lastRule */
        $lastRule = end($rules);

        $lastRule->find('css', sprintf('[data-test-channel-tab="%s"]', $channelCode))->click();
        $lastRule->fillField($fieldName, $value);

        $this->waitForLiveComponentUpdate();
    }

    public function getShippingChargesValidationErrorsCount(string $channelCode): int
    {
        return count(
            $this
                ->getElement('calculator_configuration_channel_tab_content', ['%channelCode%' => $channelCode])
                ->findAll('css', '.invalid-feedback')
        );
    }

    /**
     * @param array<string, string> $parameters
     */
    public function getValidationMessage(string $element, array $parameters = []): string
    {
        $foundElement = $this->getFieldElement($element, $parameters);
        if (null === $foundElement) {
            throw new ElementNotFoundException($this->getSession(), 'Field element');
        }

        $validationMessage = $foundElement->find('css', '.invalid-feedback');
        if (null === $validationMessage) {
            throw new ElementNotFoundException($this->getSession(), 'Validation message', 'css', '.invalid-feedback');
        }

        return $validationMessage->getText();
    }

    public function getValidationMessageForCalculatorConfiguration(string $element, string $channelCode): string
    {
        $field = $this->getFieldElement(sprintf('calculator_configuration_%s', $element), ['%channelCode%' => $channelCode])->getParent();

        return $this->getValidationMessageForElement($field);
    }

    public function getValidationMessageForLastRuleConfiguration(string $element, ?string $channelCode = null): string
    {
        $numberOfRules = count($this->getDocument()->findAll('css', '[data-test-rule]'));

        if (null === $channelCode) {
            $field = $this->getFieldElement(sprintf('rule_configuration_%s', $element), ['%position%' => $numberOfRules - 1]);
        } else {
            $field = $this->getFieldElement(
                sprintf('rule_configuration_%s', $element),
                ['%position%' => $numberOfRules - 1, '%channelCode%' => $channelCode],
            );
        }

        return $this->getValidationMessageForElement($field);
    }

    private function getValidationMessageForElement(NodeElement $element): string
    {
        $validationMessage = $element->find('css', '.invalid-feedback');
        if (null === $validationMessage) {
            throw new ElementNotFoundException(
                $this->getSession(),
                'Validation message',
                'css',
                '.invalid-feedback',
            );
        }

        return $validationMessage->getText();
    }

    /**
     * @param array<string, string> $parameters
     *
     * @throws ElementNotFoundException
     */
    private function getFieldElement(string $element, array $parameters = []): ?NodeElement
    {
        $element = $this->getElement($element, $parameters);
        while (null !== $element && !$element->hasClass('field')) {
            $element = $element->getParent();
        }

        return $element;
    }

    private function selectCalculatorConfigurationChannelTab(string $channelCode): void
    {
        if (!DriverHelper::isJavascript($this->getDriver())) {
            throw new \RuntimeException('This method can be used only with JavaScript enabled');
        }

        $this->getElement('calculator_configuration_channel_tab', ['%channelCode%' => $channelCode])->click();
    }

    private function waitForLiveComponentUpdate(): void
    {
        $form = $this->getElement('live_component');
        usleep(500000);
        $form->waitFor(1500, fn () => !$form->hasAttribute('busy'));
    }
}
