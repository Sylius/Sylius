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

namespace Sylius\Tests\Controller;

use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

final class AdminProductVariantAjaxTest extends SessionAwareAjaxTestCase
{
    /** @test */
    public function it_denies_access_to_product_variants_for_not_authenticated_user(): void
    {
        $this->client->request('GET', '/admin/ajax/product-variants/search-all');

        $response = $this->client->getResponse();

        $this->assertEquals($response->getStatusCode(), Response::HTTP_FOUND);
    }

    /** @test */
    public function it_returns_only_specified_part_of_all_product_variants_for_empty_phrase(): void
    {
        $this->loadFixturesFromFile('authentication/administrator.yml');
        $this->loadFixturesFromFiles(['resources/product_variants.yml']);

        $this->authenticateAdminUser();

        $this->client->request('GET', '/admin/ajax/product-variants/search-all?phrase=');

        $response = $this->client->getResponse();

        $this->assertResponse($response, 'ajax/product_variant/index_response', Response::HTTP_OK);
    }

    /** @test */
    public function it_throws_type_error_when_phrase_is_not_specified(): void
    {
        $this->loadFixturesFromFile('authentication/administrator.yml');
        $this->loadFixturesFromFiles(['resources/product_variants.yml']);

        $this->authenticateAdminUser();

        $this->expectException(\TypeError::class);

        $this->client->request('GET', '/admin/ajax/product-variants/search-all');
    }

    /** @test */
    public function it_returns_specific_product_variants_for_given_phrase(): void
    {
        $this->loadFixturesFromFile('authentication/administrator.yml');
        $this->loadFixturesFromFiles(['resources/product_variants.yml']);

        $this->authenticateAdminUser();

        $this->client->request('GET', '/admin/ajax/product-variants/search-all?phrase=LA');

        $response = $this->client->getResponse();

        $productVariants = json_decode($response->getContent());

        $this->assertEquals('LARGE_MUG', $productVariants[0]->code);
    }

    private function authenticateAdminUser(): void
    {
        $adminUserRepository = self::$kernel->getContainer()->get('sylius.repository.admin_user');
        $user = $adminUserRepository->findOneByEmail('admin@sylius.com');

        $session = self::$kernel->getContainer()->get('request_stack')->getSession();
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
