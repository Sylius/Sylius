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
use Sylius\Behat\Behaviour\Toggles;
use Sylius\Behat\Page\Admin\Crud\UpdatePage as BaseUpdatePage;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
class UpdatePage extends BaseUpdatePage implements UpdatePageInterface
{
    use ChecksCodeImmutability;
    use Toggles;

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
    public function nameIt($name, $languageCode)
    {
        $this->getDocument()->fillField(sprintf('sylius_payment_method_translations_%s_name', $languageCode), $name);
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
    public function isFactoryNameFieldDisabled()
    {
        return 'disabled' === $this->getElement('factory_name')->getAttribute('disabled');
    }

    /**
     * {@inheritdoc}
     */
    public function isAvailableInChannel($channelName)
    {
        return $this->getElement('channel', ['%channel%' => $channelName])->hasAttribute('checked');
    }

    /**
     * {@inheritdoc}
     */
    public function getPaymentMethodInstructions($language)
    {
        return $this->getElement('instructions', ['%language%' => $language])->getText();
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
            'channel' => '.checkbox:contains("%channel%") input',
            'code' => '#sylius_payment_method_code',
            'enabled' => '#sylius_payment_method_enabled',
            'factory_name' => '#sylius_payment_method_gatewayConfig_factoryName',
            'instructions' => '#sylius_payment_method_translations_%language%_instructions',
            'name' => '#sylius_payment_method_translations_en_US_name',
        ]);
    }
}
