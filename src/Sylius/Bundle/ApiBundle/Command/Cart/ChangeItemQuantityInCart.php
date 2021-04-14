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

namespace Sylius\Bundle\ApiBundle\Command\Cart;

use Sylius\Bundle\ApiBundle\Command\OrderItemIdAwareInterface;
use Sylius\Bundle\ApiBundle\Command\OrderTokenValueAwareInterface;

/** @experimental */
class ChangeItemQuantityInCart implements OrderTokenValueAwareInterface, OrderItemIdAwareInterface
{
    /** @var string|null */
    public $orderTokenValue;

    /** @var mixed|null */
    public $orderItemId;

    /**
     * @var int
     * @psalm-immutable
     */
    public $newQuantity;

    public function __construct(int $newQuantity)
    {
        $this->newQuantity = $newQuantity;
    }

    public static function createFromData(string $tokenValue, string $orderItemId, int $newQuantity): self
    {
        $command = new self($newQuantity);

        $command->orderTokenValue = $tokenValue;
        $command->orderItemId = $orderItemId;

        return $command;
    }

    public function getOrderTokenValue(): ?string
    {
        return $this->orderTokenValue;
    }

    public function setOrderTokenValue(?string $orderTokenValue): void
    {
        $this->orderTokenValue = $orderTokenValue;
    }

    public function getOrderItemId(): ?string
    {
        return $this->orderItemId;
    }

    public function setOrderItemId(?string $orderItemId): void
    {
        $this->orderItemId = $orderItemId;
    }
}
