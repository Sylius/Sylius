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

namespace Sylius\Component\Core\Storage;

interface CartStorageInterface
{
    /**
     * @param string $channelCode
     *
     * @return bool
     */
    public function hasCartId(string $channelCode): bool;

    /**
     * @param string $channelCode
     *
     * @return mixed
     */
    public function getCartId(string $channelCode);

    /**
     * @param string $channelCode
     * @param mixed $cartId
     */
    public function setCartId(string $channelCode, $cartId): void;

    /**
     * @param string $channelCode
     */
    public function removeCartId(string $channelCode): void;
}
