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

use Behat\Mink\Exception\ElementNotFoundException;
use Sylius\Behat\Behaviour\ChecksCodeImmutability;
use Sylius\Behat\Behaviour\Toggles;
use Sylius\Behat\Page\Admin\Crud\UpdatePage as BaseUpdatePage;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class UpdatePage extends BaseUpdatePage implements UpdatePageInterface
{
    use ChecksCodeImmutability;
    use Toggles;

    /**
     * {@inheritdoc}
     */
    public function isAvailableInChannel($channelName)
    {
        return $this->getElement('channel', ['%channel%' => $channelName])->hasAttribute('checked');
    }

    public function removeZone()
    {
        $this->getDocument()->selectFieldOption('Zone', 'Select');
    }

    /**
     * {@inheritdoc}
     */
    public function getValidationMessageForAmount($channelCode)
    {
        $foundElement = $this->getElement('amount', ['%channelCode%' => $channelCode]);
        if (null === $foundElement) {
            throw new ElementNotFoundException($this->getSession(), 'Field element');
        }

        $validationMessage = $foundElement->find('css', '.sylius-validation-error');
        if (null === $validationMessage) {
            throw new ElementNotFoundException($this->getSession(), 'Validation message', 'css', '.sylius-validation-error');
        }

        return $validationMessage->getText();
    }

    /**
     * {@inheritdoc}
     */
    protected function getCodeElement()
    {
        return $this->getElement('code');
    }

    /**
     * {@inheritdoc}
     */
    protected function getToggleableElement()
    {
        return $this->getElement('enabled');
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefinedElements()
    {
        return array_merge(parent::getDefinedElements(), [
            'amount' => '#sylius_shipping_method_configuration_%channelCode%_amount',
            'channel' => '.checkbox:contains("%channel%") input',
            'code' => '#sylius_shipping_method_code',
            'enabled' => '#sylius_shipping_method_enabled',
            'name' => '#sylius_shipping_method_translations_en_US_name',
            'zone' => '#sylius_shipping_method_zone',
        ]);
    }
}
