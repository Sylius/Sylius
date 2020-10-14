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

use Sylius\Bundle\ApiBundle\Command\OrderTokenValueAwareInterface;

/** @experimental */
class ChangeItemQuantityInCart implements OrderTokenValueAwareInterface
{
    /** @var string|null */
    public $orderTokenValue;

    /**
     * @var string
     * @psalm-immutable
     */
    public $orderItemId;

    /**
     * @var int
     * @psalm-immutable
     */
    public $newQuantity;

    public function __construct(string $orderItemId, int $newQuantity)
    {
        $this->orderItemId = $orderItemId;
        $this->newQuantity = $newQuantity;
    }

    public static function createFromData(string $tokenValue, string $orderItemId, int $newQuantity): self
    {
        $command = new self($orderItemId, $newQuantity);

        $command->orderTokenValue = $tokenValue;

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
}
