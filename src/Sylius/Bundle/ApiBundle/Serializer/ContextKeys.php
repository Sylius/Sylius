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

namespace Sylius\Bundle\ApiBundle\Serializer;

class ContextKeys
{
    public const CHANNEL = 'sylius_api_channel';

    public const LOCALE_CODE = 'sylius_api_locale_code';

    public const HTTP_REQUEST_METHOD_TYPE = 'sylius_api_http_method_request_type';

    private function __construct()
    {
    }
}
