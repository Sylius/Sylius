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

namespace Sylius\Bundle\PaymentBundle\Normalizer;

use Symfony\Component\HttpFoundation\Request;

/** @experimental */
interface SymfonyRequestNormalizerInterface
{
    /**
     * @return array{
     *      'http_request'?: array{
     *          'uri'?: string,
     *          'method'?: string,
     *          'query'?: array<string, array<int, bool|float|int|string>|bool|float|int|string>,
     *          'request'?: array<string, array<int, bool|float|int|string>|bool|float|int|string>,
     *          'headers'?: array<string, array<int, string|null>>,
     *          'content'?: string,
     *          'client_ip'?: string,
     *      },
     *  }
     */
    public function normalize(Request $request): array;
}
