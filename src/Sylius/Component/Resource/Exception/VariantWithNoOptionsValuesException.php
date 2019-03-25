<?php

declare(strict_types=1);

namespace Sylius\Component\Resource\Exception;

final class VariantWithNoOptionsValuesException extends \Exception
{
    public function __construct()
    {
        parent::__construct('sylius.product_variant.cannot_generate_variants');
    }
}
