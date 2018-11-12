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

use Sylius\Behat\Page\Admin\Crud\CreatePageInterface as BaseCreatePageInterface;

interface CreatePageInterface extends BaseCreatePageInterface
{
    public function enable();

    public function disable();

    public function nameIt(string $name, string $languageCode);

    public function specifyCode(string $code);

    public function checkChannel(string $channelName);

    public function describeIt(string $description, string $languageCode);

    public function setInstructions(string $instructions, string $languageCode);

    public function setPaypalGatewayUsername(string $username);

    public function setPaypalGatewayPassword(string $password);

    public function setPaypalGatewaySignature(string $signature);

    public function setStripeSecretKey(string $secretKey);

    public function setStripePublishableKey(string $publishableKey);

    public function isCodeDisabled(): bool;

    public function isPaymentMethodEnabled(): bool;
}
