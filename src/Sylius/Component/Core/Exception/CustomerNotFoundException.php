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

namespace Sylius\Component\Core\Exception;

class CustomerNotFoundException extends \RuntimeException
{
    public function __construct(?string $message = null, \Exception $previousException = null)
    {
        parent::__construct($message ?: 'Customer could not be found.', 0, $previousException);
    }

    public static function withEmail(string $email): self
    {
        return new self(sprintf('Customer with email "%s" does not exist.', $email));
    }
}
