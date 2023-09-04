<?php

declare(strict_types=1);

namespace Sylius\Bundle\ApiBundle\Exception;

/** @experimental */
final class ProductAttributeCannotBeRemoved extends \RuntimeException
{
    public function __construct()
    {
        parent::__construct('Cannot delete, the product attribute is in use.');
    }
}
