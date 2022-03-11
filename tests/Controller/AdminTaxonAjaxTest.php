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

namespace Sylius\Tests\Controller;

use ApiTestCase\JsonApiTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

final class AdminTaxonAjaxTest extends JsonApiTestCase
{
    /** @test */
    public function it_denies_access_to_taxons_for_not_authenticated_user(): void
    {
        $this->client->request('GET', '/admin/ajax/taxons/search');

        $response = $this->client->getResponse();

        $this->assertEquals($response->getStatusCode(), Response::HTTP_FOUND);
    }

    /** @test */
    public function it_returns_taxons_for_empty_phrase(): void
    {
        $this->loadFixturesFromFile('authentication/administrator.yml');
        $this->loadFixturesFromFiles(['resources/taxons.yml']);

        $this->authenticateAdminUser();

        $this->client->request('GET', '/admin/ajax/taxons/search?phrase=');

        $response = $this->client->getResponse();

        $this->assertResponse($response, 'ajax/taxon/index_response', Response::HTTP_OK);
    }

    /** @test */
    public function it_returns_taxons_when_phrase_is_not_specified(): void
    {
        $this->loadFixturesFromFile('authentication/administrator.yml');
        $this->loadFixturesFromFiles(['resources/taxons.yml']);

        $this->authenticateAdminUser();

        $this->client->request('GET', '/admin/ajax/taxons/search');

        $response = $this->client->getResponse();

        $this->assertResponse($response, 'ajax/taxon/index_response', Response::HTTP_OK);
    }

    /** @test */
    public function it_returns_specific_taxons_for_given_phrase(): void
    {
        $this->loadFixturesFromFile('authentication/administrator.yml');
        $this->loadFixturesFromFiles(['resources/taxons.yml']);

        $this->authenticateAdminUser();

        $this->client->request('GET', '/admin/ajax/taxons/search?phrase=men');

        $response = $this->client->getResponse();

        $this->assertResponse($response, 'ajax/taxon/specific_taxon_response', Response::HTTP_OK);
    }

    private function authenticateAdminUser(): void
    {
        $adminUserRepository = self::$container->get('sylius.repository.admin_user');
        $user = $adminUserRepository->findOneByEmail('admin@sylius.com');

        $session = self::$container->get('session');
        $firewallName = 'admin';
        $firewallContext = 'admin';

        /** @deprecated parameter credential was deprecated in Symfony 5.4, so in Sylius 1.11 too, in Sylius 2.0 providing 4 arguments will be prohibited. */
        if (3 === (new \ReflectionClass(UsernamePasswordToken::class))->getConstructor()->getNumberOfParameters()) {
            $token = new UsernamePasswordToken($user, $firewallName, $user->getRoles());
        } else {
            $token = new UsernamePasswordToken($user, null, $firewallName, $user->getRoles());
        }

        $session->set(sprintf('_security_%s', $firewallContext), serialize($token));
        $session->save();

        $cookie = new Cookie($session->getName(), $session->getId());
        $this->client->getCookieJar()->set($cookie);
    }
}
