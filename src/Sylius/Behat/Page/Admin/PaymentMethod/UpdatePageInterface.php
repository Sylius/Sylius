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

use Sylius\Behat\Page\Admin\Crud\UpdatePageInterface as BaseUpdatePageInterface;

interface UpdatePageInterface extends BaseUpdatePageInterface
{
    public function enable(): void;

    public function disable(): void;

    public function nameIt(string $name, string $languageCode): void;

    public function setPaypalGatewayUsername(string $username): void;

    public function setPaypalGatewayPassword(string $password): void;

    public function setPaypalGatewaySignature(string $signature): void;

    public function isCodeDisabled(): bool;

    public function isFactoryNameFieldDisabled(): bool;

    public function isPaymentMethodEnabled(): bool;

    public function isAvailableInChannel(string $channelName): bool;

    public function getPaymentMethodInstructions(string $language): string;
}
