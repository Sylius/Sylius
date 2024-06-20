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

use Sylius\Behat\Element\Admin\Crud\FormElement as BaseFormElement;
use Sylius\Behat\Service\DriverHelper;
use Sylius\Behat\Service\TabsHelper;

final class FormElement extends BaseFormElement implements FormElementInterface
{
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

    public function disable(): void
    {
        $this->getElement('enabled')->uncheck();
    }

    public function enable(): void
    {
        $this->getElement('enabled')->check();
    }

    public function checkChannel(string $channelCode): void
    {
        $this->getElement('channel', ['%channelCode%' => $channelCode])->check();
    }

    public function hasCheckedChannel(string $channelCode): bool
    {
        return $this->getElement('channel', ['%channelCode%' => $channelCode])->isChecked();
    }

    public function setCalculatorConfigurationAmountForChannel(string $channelCode, ?int $amount): void
    {
        $this->selectCalculatorConfigurationChannelTab($channelCode);

        $this->getElement('calculator_configuration_amount', ['%channelCode%' => $channelCode])->setValue((string) $amount);
    }

    public function chooseCalculator(string $calculatorName): void
    {
        $this->getElement('calculator')->selectOption($calculatorName);
        $this->waitForFormUpdate();
    }

    public function addRule(string $type): void
    {
        $this->getElement('add_rule_button', ['%type%' => $type])->press();
        $this->waitForFormUpdate();
    }

    public function fillLastRuleOption(string $fieldName, string $value): void
    {
        $lastRule = $this->getElement('last_rule');

        $lastRule->fillField($fieldName, $value);
    }

    public function fillLastRuleOptionForChannel(string $channelCode, string $fieldName, string $value): void
    {
        $lastRule = $this->getElement('last_rule');

        TabsHelper::switchTab($this->getSession(), $lastRule, $channelCode);

        $lastRule->find('css', sprintf('[id$="_configuration_%s"]', $channelCode))->fillField($fieldName, $value);
    }

    public function getShippingChargesValidationErrorsCount(string $channelCode): int
    {
        return count(
            $this
                ->getElement('calculator_configuration_channel_tab_content', ['%channelCode%' => $channelCode])
                ->findAll('css', '.invalid-feedback'),
        );
    }

    public function setField(string $field, string $value): void
    {
        $this->getDocument()->fillField($field, $value);
    }

    /**
     * @return array<string, string>
     */
    protected function getDefinedElements(): array
    {
        return array_merge(
            parent::getDefinedElements(), [
            'add_rule_button' => '[data-test-rules] [data-test-add-%type%]',
            'calculator' => '#sylius_admin_shipping_method_calculator',
            'calculator_configuration_amount' => '#sylius_admin_shipping_method_configuration_%channelCode%_amount',
            'calculator_configuration_channel_tab' => '[data-test-calculator-configuration] [data-test-channel-tab="%channelCode%"]',
            'calculator_configuration_channel_tab_content' => '[data-test-calculator-configuration] [data-test-channel-tab-content="%channelCode%"]',
            'channel' => '[name="sylius_admin_shipping_method[channels][]"][value="%channelCode%"]',
            'code' => '#sylius_admin_shipping_method_code',
            'description' => '#sylius_admin_shipping_method_translations_%localeCode%_description',
            'enabled' => '#sylius_admin_shipping_method_enabled',
            'last_rule' => '[data-test-rules] [data-test-entry-row]:last-child',
            'last_rule_amount' => '[data-test-rules] [data-test-entry-row]:last-child [id$="_configuration_%channelCode%_amount"]',
            'last_rule_weight' => '[data-test-rules] [data-test-entry-row]:last-child [id$="_configuration_weight"]',
            'name' => '#sylius_admin_shipping_method_translations_%localeCode%_name',
            'position' => '#sylius_admin_shipping_method_position',
            'zone' => '#sylius_admin_shipping_method_zone',
        ]);
    }

    private function selectCalculatorConfigurationChannelTab(string $channelCode): void
    {
        if (!DriverHelper::isJavascript($this->getDriver())) {
            throw new \RuntimeException('This method can be used only with JavaScript enabled');
        }

        $this->getElement('calculator_configuration_channel_tab', ['%channelCode%' => $channelCode])->click();
    }
}
