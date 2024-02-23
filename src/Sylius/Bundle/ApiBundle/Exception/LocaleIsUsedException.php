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

use Exception;

class LocaleIsUsedException extends Exception
{
    public function __construct(
        string $localeCode,
        int $code = 0,
        ?\Throwable $previous = null,
    ) {
        $message = sprintf('Locale "%s" is used.', $localeCode);

        parent::__construct($message, $code, $previous);
    }
}
