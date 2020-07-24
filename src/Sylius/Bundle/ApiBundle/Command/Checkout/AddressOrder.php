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

namespace Sylius\Bundle\ApiBundle\Command\Checkout;

use Sylius\Bundle\ApiBundle\Command\OrderTokenValueAwareInterface;
use Sylius\Component\Core\Model\Address;
use Sylius\Component\Core\Model\AddressInterface;

class AddressOrder implements OrderTokenValueAwareInterface
{
    /** @var string */
    public $orderTokenValue;

    /**
     * @var string
     * @psalm-immutable
     */
    public $email;

    /**
     * @var Address
     * @psalm-immutable
     */
    public $billingAddress;

    public function __construct(string $email, Address $billingAddress)
    {
        $this->email = $email;
        $this->billingAddress = $billingAddress;
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
