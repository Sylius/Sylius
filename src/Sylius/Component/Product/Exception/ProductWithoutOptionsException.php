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

namespace Sylius\Component\Product\Exception;

final class ProductWithoutOptionsException extends \Exception
{
    public function __construct(?\Exception $previousException = null)
    {
        parent::__construct('Cannot generate variants for a product without options.', 0, $previousException);
    }
}
