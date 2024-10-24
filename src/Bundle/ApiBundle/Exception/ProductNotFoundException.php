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

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

final class ProductNotFoundException extends HttpException
{
    public function __construct(int $statusCode = Response::HTTP_NOT_FOUND)
    {
        parent::__construct($statusCode, 'Product not found.');
    }
}
