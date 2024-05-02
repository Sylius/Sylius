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

final class PromotionNotFoundException extends NotFoundHttpException
{
    /** @param array<array-key, mixed> $headers */
    public function __construct(
        string $promotionCode,
        string $message = 'Promotion with the "%s" code not found.',
        ?\Throwable $previous = null,
        int $code = 0,
        array $headers = [],
    ) {
        parent::__construct(
            sprintf($message, $promotionCode),
            $previous,
            $code,
            $headers,
        );
    }
}
