<?php

declare(strict_types=1);

namespace Sylius\Bundle\ApiBundle\Exception;

/** @experimental */
final class ProductAttributeCannotBeRemoved extends \RuntimeException
{
    public function __construct(
        string $message = 'Cannot delete, the product attribute is in use.',
        int $code = 0,
        \Throwable $previous = null,
    ) {
        parent::__construct($message, $code, $previous);
    }
}
