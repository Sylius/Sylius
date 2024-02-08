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

namespace Sylius\Bundle\ApiBundle\Application\Tests;

use ApiPlatform\Core\Bridge\Symfony\Bundle\Test\ApiTestCase;
use Symfony\Component\HttpFoundation\Response;

class DisablingDocumentationTest extends ApiTestCase
{
    use SetUpTestsTrait;

    public function setUp(): void
    {
        $this->setFixturesFiles([]);

        $this->setUpTest();
    }

    /** @test */
    public function it_disables_documentation(): void
    {
        static::createClient()->request(
            'GET',
            'api/v2/docs',
        );

        self::assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }
}
