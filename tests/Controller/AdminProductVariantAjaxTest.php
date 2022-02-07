<?php

namespace Sylius\Tests\Controller;

use ApiTestCase\JsonApiTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class AdminProductVariantAjaxTest extends JsonApiTestCase
{
    /**
     * @test
     */
    public function it_denies_access_to_product_variants_for_not_authenticated_user()
    {
        $this->client->request('GET', '/admin/ajax/product-variants/search-all');

        $response = $this->client->getResponse();

        $this->assertEquals($response->getStatusCode(), Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @test
     */
    public function it_returns_only_specified_part_of_all_product_variants()
    {
        $this->authenticateAdminUser();

        $this->client->request('GET', '/admin/ajax/product-variants/search-all');

        $response = $this->client->getResponse();

        $expectedData = '[
            {
                "descriptor": "S (Everyday_white_basic_T_Shirt-variant-0)",
                "id": 1,
                "code": "Everyday_white_basic_T_Shirt-variant-0"
            },
            {
                "descriptor": "M (Everyday_white_basic_T_Shirt-variant-1)",
                "id": 2,
                "code": "Everyday_white_basic_T_Shirt-variant-1"
            },
            {
                "descriptor": "L (Everyday_white_basic_T_Shirt-variant-2)",
                "id": 3,
                "code": "Everyday_white_basic_T_Shirt-variant-2"
            },
            {
                "descriptor": "XL (Everyday_white_basic_T_Shirt-variant-3)",
                "id": 4,
                "code": "Everyday_white_basic_T_Shirt-variant-3"
            },
            {
                "descriptor": "XXL (Everyday_white_basic_T_Shirt-variant-4)",
                "id": 5,
                "code": "Everyday_white_basic_T_Shirt-variant-4"
            }
        ]';

        $this->assertEquals($response->getContent(), $expectedData);
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
