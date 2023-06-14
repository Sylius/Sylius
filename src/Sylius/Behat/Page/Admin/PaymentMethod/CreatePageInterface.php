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

use Sylius\Behat\Page\Admin\Crud\CreatePageInterface as BaseCreatePageInterface;

interface CreatePageInterface extends BaseCreatePageInterface
{
    public function enable(): void;

    public function disable(): void;

    public function cancelChanges(): void;

    public function nameIt(string $name, string $languageCode): void;

    public function specifyCode(string $code): void;

    public function checkChannel(string $channelName): void;

    public function describeIt(string $description, string $languageCode): void;

    public function setInstructions(string $instructions, string $languageCode): void;

    public function setPaypalGatewayUsername(string $username): void;

    public function setPaypalGatewayPassword(string $password): void;

    public function setPaypalGatewaySignature(string $signature): void;

    public function setStripeSecretKey(string $secretKey): void;

    public function setStripePublishableKey(string $publishableKey): void;

    public function isCodeDisabled(): bool;

    public function isPaymentMethodEnabled(): bool;
}
