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

/** @experimental */
final class ProductCannotBeRemoved extends \RuntimeException
{
    public function __construct(string $message = 'Cannot delete, the product is in use.')
    {
        parent::__construct($message);
    }
}
