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

namespace Sylius\Bundle\ApiBundle\Tests\ApiPlatform\OpenApi\Documentation;

use ApiPlatform\OpenApi\Model\Info;
use ApiPlatform\OpenApi\Model\PathItem;
use ApiPlatform\OpenApi\Model\Paths;
use ApiPlatform\OpenApi\OpenApi;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\ApiBundle\OpenApi\Documentation\DocumentationModifierInterface;
use Sylius\Bundle\ApiBundle\OpenApi\Documentation\NotPrefixedRoutesRemovalDocumentationModifier;

final class NotPrefixedRoutesRemovalDocumentationModifierTest extends TestCase
{
    private NotPrefixedRoutesRemovalDocumentationModifier $notApiRoutesRemovalDocumentationModifier;

    protected function setUp(): void
    {
        $this->notApiRoutesRemovalDocumentationModifier = new NotPrefixedRoutesRemovalDocumentationModifier(['/api/v2', '/custom-api']);
    }

    /** @test */
    public function it_implements_the_documentation_modifier_interface(): void
    {
        $this->assertInstanceOf(DocumentationModifierInterface::class, $this->notApiRoutesRemovalDocumentationModifier);
    }

    /** @test */
    public function it_removes_operations_without_api_route_prefix(): void
    {
        $paths = new Paths();
        $paths->addPath('/api/v2/admin/currencies', new PathItem());
        $paths->addPath('/api/v2/shop/currencies', new PathItem());
        $paths->addPath('/admin/currencies', new PathItem());
        $paths->addPath('/shop/currencies', new PathItem());
        $paths->addPath('/custom-api/currencies', new PathItem());

        $openApi = new OpenApi(new Info('title', 'version'), [], $paths);

        $modifiedOpenApi = $this->notApiRoutesRemovalDocumentationModifier->modify($openApi);
        $modifiedPaths = $modifiedOpenApi->getPaths()->getPaths();

        $this->assertArrayHasKey('/api/v2/admin/currencies', $modifiedPaths);
        $this->assertArrayHasKey('/api/v2/shop/currencies', $modifiedPaths);
        $this->assertArrayHasKey('/custom-api/currencies', $modifiedPaths);
        $this->assertArrayNotHasKey('/admin/currencies', $modifiedPaths);
        $this->assertArrayNotHasKey('/shop/currencies', $modifiedPaths);
    }
}
