<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Component\Resource\Exception;

final class VariantWithNoOptionsValuesException extends \Exception
{
    public function __construct()
    {
        parent::__construct('sylius.product_variant.cannot_generate_variants');
    }
}
