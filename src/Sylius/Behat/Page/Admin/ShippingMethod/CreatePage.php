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

namespace Sylius\Behat\Page\Admin\ShippingMethod;

use Behat\Mink\Driver\Selenium2Driver;
use Behat\Mink\Exception\ElementNotFoundException;
use Sylius\Behat\Behaviour\SpecifiesItsCode;
use Sylius\Behat\Page\Admin\Crud\CreatePage as BaseCreatePage;
use Sylius\Component\Core\Formatter\StringInflector;

class CreatePage extends BaseCreatePage implements CreatePageInterface
{
    use SpecifiesItsCode;

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
            sprintf('sylius_shipping_method_translations_%s_description', $languageCode), $description
        );
    }

    public function specifyAmountForChannel(string $channelCode, string $amount): void
    {
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
        if ($this->getDriver() instanceof Selenium2Driver) {
            $this->getElement('channel', ['%channel%' => $channelName])->click();

            return;
        }

        $this->getDocument()->checkField($channelName);
    }

    public function getValidationMessageForAmount(string $channelCode): string
    {
        $foundElement = $this->getFieldElement('amount', ['%channelCode%' => $channelCode]);
        if (null === $foundElement) {
            throw new ElementNotFoundException($this->getSession(), 'Field element');
        }

        $validationMessage = $foundElement->find('css', '.sylius-validation-error');
        if (null === $validationMessage) {
            throw new ElementNotFoundException(
                $this->getSession(),
                'Validation message',
                'css',
                '.sylius-validation-error'
            );
        }

        return $validationMessage->getText();
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'amount' => '#sylius_shipping_method_configuration_%channelCode%_amount',
            'channel' => '#sylius_shipping_method_channels .ui.checkbox:contains("%channel%")',
            'calculator' => '#sylius_shipping_method_calculator',
            'code' => '#sylius_shipping_method_code',
            'name' => '#sylius_shipping_method_translations_en_US_name',
            'zone' => '#sylius_shipping_method_zone',
        ]);
    }

    /**
     * @throws ElementNotFoundException
     */
    private function getFieldElement(string $element, array $parameters = []): ?\Behat\Mink\Element\NodeElement
    {
        $element = $this->getElement(StringInflector::nameToCode($element), $parameters);
        while (null !== $element && !$element->hasClass('field')) {
            $element = $element->getParent();
        }

        return $element;
    }
}
