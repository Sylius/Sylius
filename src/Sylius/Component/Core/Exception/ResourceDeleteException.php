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

class ResourceDeleteException extends \RuntimeException
{
    public function __construct(
        private string $resourceName,
        string $message = '',
        int $code = 0,
        ?\Throwable $previous = null,
    ) {
        if (empty($message)) {
            $message = sprintf('Cannot delete, the %s is in use.', $resourceName);
        }

        parent::__construct($message, $code, $previous);
    }

    public function getResourceName(): string
    {
        return $this->resourceName;
    }
}
