<?php

declare(strict_types=1);

namespace Sylius\Bundle\ApiBundle\OpenApi\Documentation;

use ApiPlatform\OpenApi\OpenApi;

/** @experimental */
interface DocumentationModifierInterface
{
    public function modify(OpenApi $docs): OpenApi;
}
