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

namespace Sylius\Behat\Client;

use Symfony\Component\HttpFoundation\Request as HttpRequest;

class ContentTypeGuide implements ContentTypeGuideInterface
{
    private const JSON_CONTENT_TYPE = 'application/json';

    private const PATCH_CONTENT_TYPE = 'application/merge-patch+json';

    private const LINKED_DATA_JSON_CONTENT_TYPE = 'application/ld+json';

    public function guide(string $method): string
    {
        if ($method === HttpRequest::METHOD_PATCH) {
            return self::PATCH_CONTENT_TYPE;
        }

        if ($method === HttpRequest::METHOD_PUT) {
            return self::LINKED_DATA_JSON_CONTENT_TYPE;
        }

        return self::JSON_CONTENT_TYPE;
    }
}
