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

namespace Sylius\Behat\Exception;

final class SharedStorageElementNotFoundException extends \InvalidArgumentException
{
    public function __construct(string $key = '', int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct(
            sprintf('No element found in shared storage with key "%s"', $key),
            $code,
            $previous,
        );
    }
}
