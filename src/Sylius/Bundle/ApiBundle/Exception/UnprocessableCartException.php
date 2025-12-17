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

namespace Sylius\Bundle\ApiBundle\Exception;

use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

final class UnprocessableCartException extends UnprocessableEntityHttpException
{
    public function __construct(
        ?\Throwable $previous = null,
        int $code = 0,
        array $headers = [],
    ) {
        parent::__construct('Cart with given token has not been found.', $previous, $code, $headers);
    }
}
