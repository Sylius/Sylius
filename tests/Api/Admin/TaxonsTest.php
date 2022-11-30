<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Tests\Api\Admin;

use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Tests\Api\JsonApiTestCase;
use Sylius\Tests\Api\Utils\AdminUserLoginTrait;
use Symfony\Component\HttpFoundation\Response;

final class TaxonsTest extends JsonApiTestCase
{
    use AdminUserLoginTrait;

    /** @test */
    public function it_gets_a_taxon(): void
    {
        $fixtures = $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'taxonomy.yaml']);
        $header = $this->getLoggedHeader();

        /** @var TaxonInterface $taxon */
        $taxon = $fixtures['category_taxon'];

        $this->client->request(
            'GET',
            sprintf('/api/v2/admin/taxons/%s', $taxon->getCode()),
            [],
            [],
            $header,
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/taxon/get_taxon_response',
            Response::HTTP_OK,
        );
    }

    /** @test */
    public function it_gets_taxons(): void
    {
        $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'taxonomy.yaml']);
        $header = $this->getLoggedHeader();

        $this->client->request(
            'GET',
            '/api/v2/admin/taxons',
            [],
            [],
            $header,
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/taxon/get_taxons_response',
            Response::HTTP_OK,
        );
    }

    private function getLoggedHeader(): array
    {
        $token = $this->logInAdminUser('api@example.com');
        $authorizationHeader = self::$kernel->getContainer()->getParameter('sylius.api.authorization_header');
        $header['HTTP_' . $authorizationHeader] = 'Bearer ' . $token;

        return array_merge($header, self::CONTENT_TYPE_HEADER);
    }
}
