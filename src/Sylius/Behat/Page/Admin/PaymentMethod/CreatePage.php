<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Page\Admin\PaymentMethod;

use Sylius\Behat\Behaviour\ChecksCodeImmutability;
use Sylius\Behat\Behaviour\SpecifiesItsCode;
use Sylius\Behat\Behaviour\Toggles;
use Sylius\Behat\Page\Admin\Crud\CreatePage as BaseCreatePage;

/**
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
class CreatePage extends BaseCreatePage implements CreatePageInterface
{
    use ChecksCodeImmutability;
    use Toggles;
    use SpecifiesItsCode;

    /**
     * {@inheritdoc}
     */
    public function nameIt($name, $languageCode)
    {
        $this->getDocument()->fillField(
            sprintf('sylius_payment_method_translations_%s_name', $languageCode), $name
        );
    }

    /**
     * {@inheritdoc}
     */
    public function checkChannel($channelName)
    {
        $this->getDocument()->checkField($channelName);
    }

    /**
     * {@inheritdoc}
     */
    public function describeIt($description, $languageCode)
    {
        $this->getDocument()->fillField(
            sprintf('sylius_payment_method_translations_%s_description', $languageCode), $description
        );
    }

    /**
     * {@inheritdoc}
     */
    public function setInstructions($instructions, $languageCode)
    {
        $this->getDocument()->fillField(
            sprintf('sylius_payment_method_translations_%s_instructions', $languageCode), $instructions
        );
    }

    /**
     * {@inheritdoc}
     */
    public function setPaypalGatewayUsername($username)
    {
        $this->getDocument()->fillField('Username', $username);
    }

    /**
     * {@inheritdoc}
     */
    public function setPaypalGatewayPassword($password)
    {
        $this->getDocument()->fillField('Password', $password);
    }

    /**
     * {@inheritdoc}
     */
    public function setPaypalGatewaySignature($signature)
    {
        $this->getDocument()->fillField('Signature', $signature);
    }

    /**
     * {@inheritdoc}
     */
    public function setStripeSecretKey($secretKey)
    {
        $this->getDocument()->fillField('Secret key', $secretKey);
    }

    /**
     * {@inheritdoc}
     */
    public function setStripePublishableKey($publishableKey)
    {
        $this->getDocument()->fillField('Publishable key', $publishableKey);
    }

    /**
     * {@inheritdoc}
     */
    public function isPaymentMethodEnabled()
    {
        return (bool) $this->getToggleableElement()->getValue();
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
            'code' => '#sylius_payment_method_code',
            'enabled' => '#sylius_payment_method_enabled',
            'gateway_name' => '#sylius_payment_method_gatewayConfig_gatewayName',
            'name' => '#sylius_payment_method_translations_en_US_name',
            'paypal_password' => '#sylius_payment_method_gatewayConfig_config_password',
        ]);
    }
}
