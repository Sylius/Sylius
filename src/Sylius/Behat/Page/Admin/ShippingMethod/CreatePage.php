<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Page\Admin\ShippingMethod;

use Behat\Mink\Driver\Selenium2Driver;
use Behat\Mink\Exception\ElementNotFoundException;
use Sylius\Behat\Behaviour\SpecifiesItsCode;
use Sylius\Behat\Page\Admin\Crud\CreatePage as BaseCreatePage;
use Sylius\Component\Core\Formatter\StringInflector;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class CreatePage extends BaseCreatePage implements CreatePageInterface
{
    use SpecifiesItsCode;

    /**
     * {@inheritdoc}
     */
    public function specifyPosition($position)
    {
        $this->getDocument()->fillField('Position', $position);
    }

    /**
     * {@inheritdoc}
     */
    public function nameIt($name, $language)
    {
        $this->getDocument()->fillField(sprintf('sylius_shipping_method_translations_%s_name', $language), $name);
    }

    /**
     * {@inheritdoc}
     */
    public function describeIt($description, $languageCode)
    {
        $this->getDocument()->fillField(
            sprintf('sylius_shipping_method_translations_%s_description', $languageCode), $description
        );
    }

    /**
     * {@inheritdoc}
     */
    public function specifyAmountForChannel($channelCode, $amount)
    {
        $this->getElement('amount', ['%channelCode%' => $channelCode])->setValue($amount);
    }

    /**
     * {@inheritdoc}
     */
    public function chooseZone($name)
    {
        $this->getDocument()->selectFieldOption('Zone', $name);
    }

    /**
     * {@inheritdoc}
     */
    public function chooseCalculator($name)
    {
        $this->getDocument()->selectFieldOption(\Calculator::class, $name);
    }

    /**
     * {@inheritdoc}
     */
    public function checkChannel($channelName)
    {
        if ($this->getDriver() instanceof Selenium2Driver) {
            $this->getElement('channel', ['%channel%' => $channelName])->click();

            return;
        }

        $this->getDocument()->checkField($channelName);
    }

    /**
     * {@inheritdoc}
     */
    public function getValidationMessageForAmount($channelCode)
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

    /**
     * {@inheritdoc}
     */
    protected function getDefinedElements()
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
     * @param string $element
     * @param array $parameters
     *
     * @return \Behat\Mink\Element\NodeElement|null
     *
     * @throws ElementNotFoundException
     */
    private function getFieldElement($element, array $parameters = [])
    {
        $element = $this->getElement(StringInflector::nameToCode($element), $parameters);
        while (null !== $element && !$element->hasClass('field')) {
            $element = $element->getParent();
        }

        return $element;
    }
}
