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

namespace Sylius\Behat\Page\Admin\PaymentMethod;

use Sylius\Behat\Behaviour\ChecksCodeImmutability;
use Sylius\Behat\Behaviour\SpecifiesItsCode;
use Sylius\Behat\Behaviour\Toggles;
use Sylius\Behat\Page\Admin\Crud\CreatePage as BaseCreatePage;

class CreatePage extends BaseCreatePage implements CreatePageInterface
{
    use ChecksCodeImmutability;
    use Toggles;
    use SpecifiesItsCode;

    /**
     * {@inheritdoc}
     */
    public function nameIt(string $name, string $languageCode): void
    {
        $this->getDocument()->fillField(
            sprintf('sylius_payment_method_translations_%s_name', $languageCode), $name
        );
    }

    /**
     * {@inheritdoc}
     */
    public function checkChannel(string $channelName): void
    {
        $this->getDocument()->checkField($channelName);
    }

    /**
     * {@inheritdoc}
     */
    public function describeIt(string $description, string $languageCode): void
    {
        $this->getDocument()->fillField(
            sprintf('sylius_payment_method_translations_%s_description', $languageCode), $description
        );
    }

    /**
     * {@inheritdoc}
     */
    public function setInstructions(string $instructions, string $languageCode): void
    {
        $this->getDocument()->fillField(
            sprintf('sylius_payment_method_translations_%s_instructions', $languageCode), $instructions
        );
    }

    /**
     * {@inheritdoc}
     */
    public function setPaypalGatewayUsername(string $username): void
    {
        $this->getDocument()->fillField('Username', $username);
    }

    /**
     * {@inheritdoc}
     */
    public function setPaypalGatewayPassword(string $password): void
    {
        $this->getDocument()->fillField('Password', $password);
    }

    /**
     * {@inheritdoc}
     */
    public function setPaypalGatewaySignature(string $signature): void
    {
        $this->getDocument()->fillField('Signature', $signature);
    }

    /**
     * {@inheritdoc}
     */
    public function setStripeSecretKey(string $secretKey): void
    {
        $this->getDocument()->fillField('Secret key', $secretKey);
    }

    /**
     * {@inheritdoc}
     */
    public function setStripePublishableKey(string $publishableKey): void
    {
        $this->getDocument()->fillField('Publishable key', $publishableKey);
    }

    /**
     * {@inheritdoc}
     */
    public function isPaymentMethodEnabled(): bool
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
    protected function getDefinedElements(): array
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
