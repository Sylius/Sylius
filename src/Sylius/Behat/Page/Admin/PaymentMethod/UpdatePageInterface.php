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

use Sylius\Behat\Page\Admin\Crud\UpdatePageInterface as BaseUpdatePageInterface;

interface UpdatePageInterface extends BaseUpdatePageInterface
{
    public function enable(): void;

    public function disable(): void;

    /**
     * @param string $name
     * @param string $languageCode
     */
    public function nameIt(string $name, string $languageCode): void;

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
     * @return bool
     */
    public function isCodeDisabled(): bool;

    /**
     * @return bool
     */
    public function isFactoryNameFieldDisabled(): bool;

    /**
     * @return bool
     */
    public function isPaymentMethodEnabled(): bool;

    /**
     * @param string $channelName
     *
     * @return bool
     */
    public function isAvailableInChannel(string $channelName): bool;

    /**
     * @param string $language
     *
     * @return string
     */
    public function getPaymentMethodInstructions(string $language): string;
}
