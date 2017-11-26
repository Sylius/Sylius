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
    public function enable(): void;

    public function disable(): void;

    /**
     * @param string $name
     * @param string $languageCode
     */
    public function nameIt(string $name, string $languageCode): void;

    /**
     * @param string $code
     */
    public function specifyCode(string $code): void;

    /**
     * @param string $channelName
     */
    public function checkChannel(string $channelName): void;

    /**
     * @param string $description
     * @param string $languageCode
     */
    public function describeIt(string $description, string $languageCode): void;

    /**
     * @param string $instructions
     * @param string $languageCode
     */
    public function setInstructions(string $instructions, string $languageCode): void;

    /**
     * @param string $username
     */
    public function setPaypalGatewayUsername(string $username): void;

    /**
     * @param string $password
     */
    public function setPaypalGatewayPassword(string $password): void;

    /**
     * @param string $signature
     */
    public function setPaypalGatewaySignature(string $signature): void;

    /**
     * @param string $secretKey
     */
    public function setStripeSecretKey(string $secretKey): void;

    /**
     * @param string $publishableKey
     */
    public function setStripePublishableKey(string $publishableKey): void;

    /**
     * @return bool
     */
    public function isCodeDisabled(): bool;

    /**
     * @return bool
     */
    public function isPaymentMethodEnabled(): bool;
}
