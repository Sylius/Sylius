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

namespace Sylius\Bundle\ApiBundle\Tests\Stub;

use ApiPlatform\OpenApi\Model\Info;
use ApiPlatform\OpenApi\Model\Paths;
use ApiPlatform\OpenApi\OpenApi;
use Sylius\Bundle\ApiBundle\Attribute\AsDocumentationModifier;
use Sylius\Bundle\ApiBundle\OpenApi\Documentation\DocumentationModifierInterface;

#[AsDocumentationModifier(priority: 15)]
final class DocumentationModifierStub implements DocumentationModifierInterface
{
    public function modify(OpenApi $docs): OpenApi
    {
        return new OpenApi(new Info('title', '1.0.0'), [], new Paths());
    }
}
