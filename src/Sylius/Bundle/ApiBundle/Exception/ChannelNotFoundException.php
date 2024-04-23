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

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class ChannelNotFoundException extends NotFoundHttpException
{
    public function __construct(
        string $message = 'Channel not found.',
        ?\Throwable $previous = null,
        int $code = 0,
    ) {
        parent::__construct($message, $previous, $code);
    }
}
