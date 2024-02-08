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

namespace Sylius\Behat\Page\Admin\PaymentMethod;

use Behat\Mink\Element\NodeElement;
use Sylius\Behat\Behaviour\ChecksCodeImmutability;
use Sylius\Behat\Behaviour\Toggles;
use Sylius\Behat\Page\Admin\Crud\UpdatePage as BaseUpdatePage;

class UpdatePage extends BaseUpdatePage implements UpdatePageInterface
{
    use ChecksCodeImmutability;
    use Toggles;

    public function setPaypalGatewayUsername(string $username): void
    {
        $this->getDocument()->fillField('Username', $username);
    }

    public function setPaypalGatewayPassword(string $password): void
    {
        $this->getDocument()->fillField('Password', $password);
    }

    public function setPaypalGatewaySignature(string $signature): void
    {
        $this->getDocument()->fillField('Signature', $signature);
    }

    public function nameIt(string $name, string $languageCode): void
    {
        $this->getDocument()->fillField(sprintf('sylius_payment_method_translations_%s_name', $languageCode), $name);
    }

    public function isPaymentMethodEnabled(): bool
    {
        return (bool) $this->getToggleableElement()->getValue();
    }

    public function isFactoryNameFieldDisabled(): bool
    {
        return 'disabled' === $this->getElement('factory_name')->getAttribute('disabled');
    }

    public function isAvailableInChannel(string $channelName): bool
    {
        return $this->getElement('channel', ['%channel%' => $channelName])->hasAttribute('checked');
    }

    public function getPaymentMethodInstructions(string $language): string
    {
        return $this->getElement('instructions', ['%language%' => $language])->getText();
    }

    protected function getCodeElement(): NodeElement
    {
        return $this->getElement('code');
    }

    protected function getToggleableElement(): NodeElement
    {
        return $this->getElement('enabled');
    }

    protected function getDefinedElements(): array
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
