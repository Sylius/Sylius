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

namespace Sylius\Bundle\ApiBundle\Command;

/**
 * @experimental
 * @psalm-immutable
 */
class RegisterShopUser
{
    /** @var string */
    public $firstName;

    /** @var string */
    public $lastName;

    /** @var string */
    public $email;

    /** @var string */
    public $password;

    /** @var string|null */
    public $phoneNumber;

    public function __construct(
        string $firstName,
        string $lastName,
        string $email,
        string $password,
        ?string $phoneNumber = null
    ) {
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->email = $email;
        $this->password = $password;
        $this->phoneNumber = $phoneNumber;
    }
}
