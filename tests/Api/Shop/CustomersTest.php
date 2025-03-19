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

namespace Sylius\Tests\Api\Shop;

use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Tests\Api\JsonApiTestCase;
use Symfony\Component\HttpFoundation\Response;

final class CustomersTest extends JsonApiTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->setUpDefaultGetHeaders();
        $this->setUpDefaultPostHeaders();
        $this->setUpDefaultPutHeaders();
        $this->setUpDefaultPatchHeaders();
    }

    /** @test */
    public function it_gets_a_customer_as_logged_user(): void
    {
        $fixtures = $this->loadFixturesFromFiles(['authentication/shop_user.yaml']);
        /** @var CustomerInterface $customer */
        $customer = $fixtures['customer_oliver'];

        $this->requestGet(
            uri: '/api/v2/shop/customers/' . $customer->getId(),
            headers: $this->headerBuilder()->withShopUserAuthorization($customer->getEmailCanonical())->build(),
        );

        $this->assertResponse($this->client->getResponse(), 'shop/customer/get_customer_response');
    }

    /** @test */
    public function it_logs_in(): void
    {
        $this->loadFixturesFromFiles(['authentication/shop_user.yaml']);

        $this->requestPost(
            '/api/v2/shop/customers/token',
            [
                'email' => 'oliver@doe.com',
                'password' => 'sylius',
            ],
        );

        $this->assertResponse($this->client->getResponse(), 'shop/customer/log_in_customer_response');
    }

    /** @test */
    public function it_gets_customer_as_another_logged_user(): void
    {
        $fixtures = $this->loadFixturesFromFiles(['authentication/shop_user.yaml']);
        /** @var CustomerInterface $customer */
        $customer = $fixtures['customer_oliver'];

        $this->requestGet(
            uri: '/api/v2/shop/customers/' . $customer->getId(),
            headers: $this->headerBuilder()->withShopUserAuthorization('dave@doe.com')->build(),
        );

        $this->assertResponseCode($this->client->getResponse(), Response::HTTP_NOT_FOUND);
    }

    /** @test */
    public function it_gets_customer_as_guest(): void
    {
        $fixtures = $this->loadFixturesFromFiles(['authentication/shop_user.yaml']);
        /** @var CustomerInterface $customer */
        $customer = $fixtures['customer_oliver'];

        $this->requestGet('/api/v2/shop/customers/' . $customer->getId());

        $this->assertResponseCode($this->client->getResponse(), Response::HTTP_NOT_FOUND);
    }

    /** @test */
    public function it_registers_a_new_customer(): void
    {
        $this->loadFixturesFromFiles(['channel/channel.yaml']);

        $this->requestPost(
            '/api/v2/shop/customers',
            [
                'firstName' => 'John',
                'lastName' => 'Doe',
                'email' => 'shop@example.com',
                'password' => 'sylius',
                'subscribedToNewsletter' => true,
            ],
        );

        $this->assertResponseCode($this->client->getResponse(), Response::HTTP_NO_CONTENT);
    }

    /** @test */
    public function it_updates_customers_data(): void
    {
        $loadedData = $this->loadFixturesFromFiles(['authentication/shop_user.yaml']);
        /** @var CustomerInterface $customer */
        $customer = $loadedData['customer_oliver'];

        $this->requestPut(
            '/api/v2/shop/customers/' . $customer->getId(),
            [
                'email' => 'john.wick@tarasov.mob',
                'firstName' => 'John',
                'lastName' => 'Wick',
                'phoneNumber' => '666777888',
                'gender' => 'm',
                'birthday' => '2023-10-24T11:00:00.000Z',
                'subscribedToNewsletter' => true,
            ],
            headers: $this->headerBuilder()->withShopUserAuthorization($customer->getEmailCanonical())->build(),
        );

        $this->assertResponse($this->client->getResponse(), 'shop/customer/put_customer_response');
    }

    /** @test */
    public function it_sends_reset_password_email(): void
    {
        $loadedData = $this->loadFixturesFromFiles(['authentication/shop_user.yaml', 'channel/channel.yaml']);
        /** @var CustomerInterface $customer */
        $customer = $loadedData['customer_oliver'];

        $this->requestPost(
            '/api/v2/shop/customers/reset-password',
            [
                'email' => $customer->getEmailCanonical(),
                'localeCode' => 'en_US',
            ],
        );

        $this->assertResponseCode($this->client->getResponse(), Response::HTTP_ACCEPTED);
        $this->assertEmailCount(1);
    }

    /** @test */
    public function it_validates_wrong_reset_password_request(): void
    {
        $this->loadFixturesFromFiles(['authentication/shop_user.yaml', 'channel/channel.yaml']);

        $this->requestPost(
            '/api/v2/shop/customers/reset-password',
            [
                'email' => 'wrong_email',
                'localeCode' => 'te_ST',
            ],
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'shop/customer/reset_password_validation_response',
            Response::HTTP_UNPROCESSABLE_ENTITY,
        );
    }

    /** @test */
    public function it_resets_account_password(): void
    {
        $loadedData = $this->loadFixturesFromFiles(['authentication/shop_user.yaml', 'channel/channel.yaml']);

        /** @var ShopUserInterface $shopUser */
        $shopUser = $loadedData['shop_user_oliver'];
        $shopUser->setPasswordResetToken('token');
        $shopUser->setPasswordRequestedAt(new \DateTime('now'));
        $this->getEntityManager()->flush();

        $this->requestPatch(
            '/api/v2/shop/customers/reset-password/token',
            [
                'newPassword' => 'newPassword',
                'confirmNewPassword' => 'newPassword',
            ],
        );

        $this->assertResponseCode($this->client->getResponse(), Response::HTTP_ACCEPTED);
    }

    /** @test */
    public function it_prevents_from_resetting_password_with_invalid_token(): void
    {
        $this->loadFixturesFromFiles(['authentication/shop_user_with_reset_password_token.yaml', 'channel/channel.yaml']);

        $this->requestPatch(
            '/api/v2/shop/customers/reset-password/invalid_token',
            [
                'newPassword' => 'newPassword',
                'confirmNewPassword' => 'newPassword',
            ],
        );

        $this->assertResponseCode($this->client->getResponse(), Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /** @test */
    public function it_prevents_from_resetting_password_with_expired_token(): void
    {
        $this->loadFixturesFromFiles([
            'authentication/shop_user_with_expired_reset_password_token.yaml',
            'channel/channel.yaml',
        ]);

        $this->requestPatch(
            '/api/v2/shop/customers/reset-password/valid_token',
            [
                'newPassword' => 'newPassword',
                'confirmNewPassword' => 'newPassword',
            ],
        );

        $this->assertResponseCode($this->client->getResponse(), Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /** @test */
    public function it_changes_password(): void
    {
        $fixtures = $this->loadFixturesFromFiles(['authentication/shop_user.yaml', 'channel/channel.yaml']);
        /** @var CustomerInterface $customer */
        $customer = $fixtures['customer_oliver'];

        $this->requestPut(
            sprintf('/api/v2/shop/customers/%s/password', $customer->getId()),
            [
                'currentPassword' => 'sylius',
                'newPassword' => 'newPassword',
                'confirmNewPassword' => 'newPassword',
            ],
            headers: $this->headerBuilder()->withShopUserAuthorization($customer->getEmailCanonical())->build(),
        );

        $this->assertResponseCode($this->client->getResponse(), Response::HTTP_NO_CONTENT);
    }
}
