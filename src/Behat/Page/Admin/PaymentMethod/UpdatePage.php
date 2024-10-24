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

    public function nameIt(string $name, string $languageCode): void
    {
        $this->getDocument()->fillField(sprintf('sylius_admin_payment_method_translations_%s_name', $languageCode), $name);
    }

    public function enableSandboxMode(): void
    {
        $this->getElement('sandbox')->check();
    }

    public function isPaymentMethodEnabled(): bool
    {
        return (bool) $this->getToggleableElement()->getValue();
    }

    public function isPaymentMethodInSandboxMode(): bool
    {
        return $this->getElement('sandbox')->hasAttribute('checked');
    }

    public function isFactoryNameFieldDisabled(): bool
    {
        return 'disabled' === $this->getElement('factory_name')->getAttribute('disabled');
    }

    public function isAvailableInChannel(string $channelName): bool
    {
        return $this
            ->getElement('channel', ['%channel_name%' => $channelName])
            ->hasAttribute('checked')
        ;
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

    /**
     * @return array<string, string>
     */
    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'channel' => '[data-test-channel-name="%channel_name%"]',
            'code' => '[data-test-code]',
            'enabled' => '[data-test-enabled]',
            'factory_name' => '[data-test-factory-name]',
            'instructions' => '#sylius_admin_payment_method_translations_%language%_instructions',
            'name' => '#sylius_admin_payment_method_translations_en_US_name',
            'password' => '[data-test-password]',
            'publishable_key' => '[data-test-publishable-key]',
            'sandbox' => '[data-test-sandbox]',
            'secret_key' => '[data-test-secret-key]',
            'signature' => '[data-test-signature]',
            'username' => '[data-test-username]',
        ]);
    }
}
