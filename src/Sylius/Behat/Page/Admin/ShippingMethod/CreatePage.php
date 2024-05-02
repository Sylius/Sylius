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

namespace Sylius\Behat\Page\Admin\ShippingMethod;

use Behat\Mink\Element\NodeElement;
use Behat\Mink\Exception\ElementNotFoundException;
use Sylius\Behat\Behaviour\SpecifiesItsField;
use Sylius\Behat\Page\Admin\Crud\CreatePage as BaseCreatePage;
use Sylius\Behat\Service\DriverHelper;
use Sylius\Behat\Service\TabsHelper;
use Sylius\Component\Core\Formatter\StringInflector;
use Webmozart\Assert\Assert;

class CreatePage extends BaseCreatePage implements CreatePageInterface
{
    use SpecifiesItsField;

    public function specifyPosition(?int $position): void
    {
        $this->getDocument()->fillField('Position', $position);
    }

    public function nameIt(string $name, string $language): void
    {
        $this->getDocument()->fillField(sprintf('sylius_shipping_method_translations_%s_name', $language), $name);
    }

    public function describeIt(string $description, string $languageCode): void
    {
        $this->getDocument()->fillField(
            sprintf('sylius_shipping_method_translations_%s_description', $languageCode),
            $description,
        );
    }

    public function specifyAmountForChannel(string $channelCode, string $amount): void
    {
        TabsHelper::switchTab($this->getSession(), $this->getElement('calculator_configuration'), $channelCode);

        $this->getElement('amount', ['%channelCode%' => $channelCode])->setValue($amount);
    }

    public function chooseZone(string $name): void
    {
        $this->getDocument()->selectFieldOption('Zone', $name);
    }

    public function chooseCalculator(string $name): void
    {
        $this->getDocument()->selectFieldOption('Calculator', $name);
    }

    public function checkChannel($channelName): void
    {
        if (DriverHelper::isJavascript($this->getDriver())) {
            $this->getElement('channel', ['%channel%' => $channelName])->click();

            return;
        }

        $this->getDocument()->checkField($channelName);
    }

    public function getValidationMessageForAmount(string $channelCode): string
    {
        $foundElement = $this->getFieldElement('amount', ['%channelCode%' => $channelCode]);

        return $this->getValidationMessageForElement($foundElement);
    }

    public function getValidationMessageForRuleAmount(string $channelCode): string
    {
        $foundElement = $this->getChannelConfigurationOfLastRule($channelCode);

        return $this->getValidationMessageForElement($foundElement);
    }

    public function addRule(string $ruleName): void
    {
        $count = count($this->getCollectionItems('rules'));

        $this->getDocument()->clickLink('Add rule');

        $this->getDocument()->waitFor(5, fn () => $count + 1 === count($this->getCollectionItems('rules')));

        $this->selectRuleOption('Type', $ruleName);
    }

    public function selectRuleOption(string $option, string $value, bool $multiple = false): void
    {
        $this->getLastCollectionItem('rules')->find('named', ['select', $option])->selectOption($value, $multiple);
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

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'amount' => '#sylius_shipping_method_configuration_%channelCode%_amount',
            'channel' => '#sylius_shipping_method_channels .ui.checkbox:contains("%channel%")',
            'calculator' => '#sylius_shipping_method_calculator',
            'calculator_configuration' => '.ui.segment.configuration',
            'weight' => '[id*="sylius_shipping_method_rules_"][id*="_configuration_weight"]',
            'code' => '#sylius_shipping_method_code',
            'name' => '#sylius_shipping_method_translations_en_US_name',
            'zone' => '#sylius_shipping_method_zone',
            'rules' => '#sylius_shipping_method_rules',
        ]);
    }

    private function getValidationMessageForElement(NodeElement $element): string
    {
        if (null === $element) {
            throw new ElementNotFoundException($this->getSession(), 'Field element');
        }

        $validationMessage = $element->find('css', '.sylius-validation-error');
        if (null === $validationMessage) {
            throw new ElementNotFoundException(
                $this->getSession(),
                'Validation message',
                'css',
                '.sylius-validation-error',
            );
        }

        return $validationMessage->getText();
    }

    /**
     * @throws ElementNotFoundException
     */
    private function getFieldElement(string $element, array $parameters = []): ?NodeElement
    {
        $element = $this->getElement(StringInflector::nameToCode($element), $parameters);
        while (null !== $element && !$element->hasClass('field')) {
            $element = $element->getParent();
        }

        return $element;
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

    private function getChannelConfigurationOfLastRule(string $channelCode): ?NodeElement
    {
        return $this
            ->getLastCollectionItem('rules')
            ->find('css', sprintf('[id^="sylius_shipping_method_rules_"][id$="_configuration_%s"]', $channelCode))
        ;
    }
}
