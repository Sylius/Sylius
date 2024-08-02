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

namespace Sylius\Tests\Api\Admin;

use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Tests\Api\JsonApiTestCase;
use Sylius\Tests\Api\Utils\AdminUserLoginTrait;
use Symfony\Component\HttpFoundation\Response;

final class ChannelsTest extends JsonApiTestCase
{
    use AdminUserLoginTrait;

    protected function setUp(): void
    {
        parent::setUp();

        $this->setUpAdminContext();
    }

    /** @test */
    public function it_gets_a_channel(): void
    {
        $this->setUpDefaultGetHeaders();
        $fixtures = $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'channel.yaml']);

        /** @var ChannelInterface $channel */
        $channel = $fixtures['channel_web'];

        $this->requestGet('/api/v2/admin/channels/' . $channel->getCode());

        $this->assertResponseSuccessful('admin/channel/get_channel_response');
    }

    /** @test */
    public function it_gets_channels(): void
    {
        $this->setUpDefaultGetHeaders();
        $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'channel.yaml']);

        $this->requestGet('/api/v2/admin/channels');

        $this->assertResponseSuccessful('admin/channel/get_channels_response');
    }

    /** @test */
    public function it_creates_a_channel(): void
    {
        $this->setUpDefaultPostHeaders();
        $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'currency.yaml', 'locale.yaml']);

        $this->requestPost('/api/v2/admin/channels', [
            'name' => 'Web Store',
            'code' => 'WEB',
            'description' => 'Lorem ipsum',
            'hostname' => 'test.com',
            'color' => 'blue',
            'enabled' => true,
            'baseCurrency' => '/api/v2/admin/currencies/USD',
            'defaultLocale' => '/api/v2/admin/locales/en_US',
            'taxCalculationStrategy' => 'order_items_based',
            'currencies' => [],
            'locales' => ['/api/v2/admin/locales/en_US'],
            'themeName' => 'garish',
            'contactEmail' => 'contact@test.com',
            'contactPhoneNumber' => '1-800-00-00-00',
            'skippingShippingStepAllowed' => false,
            'skippingPaymentStepAllowed' => true,
            'accountVerificationRequired' => true,
            'shippingAddressInCheckoutRequired' => false,
            'shopBillingData' => [
                'company' => 'Company: Created',
                'taxId' => 'Tax ID: Created',
                'countryCode' => 'US',
                'street' => 'Street: Created',
                'city' => 'City: Created',
                'postcode' => 'Postcode: Created',
            ],
            'menuTaxon' => null,
        ]);

        $this->assertResponseCreated('admin/channel/post_channel_response');
    }

    /**
     * @test
     *
     * @dataProvider getBlankFieldsData
     * @dataProvider getTooLongFieldsData
     *
     * @param array<string, string> $inputData
     * @param array<string, string> $validation
     */
    public function it_prevents_creating_a_channel_with_invalid_data(array $inputData, array $validation): void
    {
        $this->setUpDefaultPostHeaders();
        $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'currency.yaml', 'locale.yaml']);

        $this->requestPost('/api/v2/admin/channels', $inputData);

        $this->assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->assertJsonResponseViolations($this->client->getResponse(), [$validation], false);
    }

    /** @test */
    public function it_updates_an_existing_channel(): void
    {
        $fixtures = $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'channel.yaml']);

        /** @var ChannelInterface $channel */
        $channel = $fixtures['channel_web'];

        $header = array_merge($this->logInAdminUser('api@example.com'), self::CONTENT_TYPE_HEADER);

        $this->client->request(
            method: 'PUT',
            uri: '/api/v2/admin/channels/' . $channel->getCode(),
            server: $header,
            content: json_encode([
                'defaultLocale' => '/api/v2/admin/locales/en_US',
                'locales' => ['/api/v2/admin/locales/en_US'],
                'shippingAddressInCheckoutRequired' => true,
                'shopBillingData' => [
                    'company' => 'Web Channel Company: Updated',
                    'taxId' => 'Web Channel Tax ID: Updated',
                    'countryCode' => 'PL',
                    'street' => 'Web Channel Street: Updated',
                    'city' => 'Web Channel City: Updated',
                    'postcode' => 'Web Channel Postcode: Updated',
                ],
                'taxCalculationStrategy' => 'order_items_based',
                'accountVerificationRequired' => false,
                'name' => 'Web Store',
                'description' => 'different description',
                'hostname' => 'updated-hostname.com',
                'color' => 'blue',
            ], \JSON_THROW_ON_ERROR),
        );

        $this->assertResponse(
            $this->client->getResponse(),
            'admin/channel/put_channel_response',
            Response::HTTP_OK,
        );
    }

    /** @test */
    public function it_deletes_a_channel(): void
    {
        $this->setUpDefaultDeleteHeaders();
        $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'channel.yaml']);

        $this->requestGet('/api/v2/admin/channels/MOBILE');
        $this->assertResponseCode($this->client->getResponse(), Response::HTTP_OK);

        $this->requestDelete('/api/v2/admin/channels/MOBILE');
        $this->assertResponseCode($this->client->getResponse(), Response::HTTP_NO_CONTENT);

        $this->requestGet('/api/v2/admin/channels/MOBILE');
        $this->assertResponseCode($this->client->getResponse(), Response::HTTP_NOT_FOUND);
    }

    // todo: Needs exception_to_status to be investigated

//    /** @test */
//    public function it_prevents_deleting_the_only_channel(): void
//    {
//        $this->loadFixturesFromFiles(['authentication/api_administrator.yaml', 'channel.yaml']);
//        $header = array_merge($this->logInAdminUser('api@example.com'), self::CONTENT_TYPE_HEADER);
//
//        $this->client->request(
//            method: 'DELETE',
//            uri: '/api/v2/admin/channels/MOBILE',
//            server: $header,
//        );
//        $this->client->request(
//            method: 'DELETE',
//            uri: '/api/v2/admin/channels/WEB',
//            server: $header,
//        );
//
//        $this->assertResponse(
//            $this->client->getResponse(),
//            'admin/channel/delete_channel_that_cannot_be_deleted',
//            Response::HTTP_UNPROCESSABLE_ENTITY,
//        );
//    }

    /**
     * @return \Generator<array{0: array<string, string>, 1: array<string, string>}>
     */
    public function getBlankFieldsData(): iterable
    {
        $blankFields = [
            'code' => 'channel code',
            'name' => 'channel name',
            'baseCurrency' => 'channel base currency',
            'defaultLocale' => 'channel default locale',
            'taxCalculationStrategy' => 'tax calculation strategy',
        ];
        foreach ($blankFields as $field => $messageFieldName) {
            $message = sprintf('Please enter %s.', $messageFieldName);

            yield [
                [],
                ['propertyPath' => $field, 'message' => $message],
            ];
        }
    }

    /**
     * @return \Generator<array{0: array<string, string>, 1: array<string, string>}>
     */
    public function getTooLongFieldsData(): iterable
    {
        $valueOverStringMax = str_repeat('a@', 128);

        $stringFields = [
            'name' => 'Channel name',
            'hostname' => 'Hostname',
            'color' => 'Color',
            'themeName' => 'Theme name',
            'taxCalculationStrategy' => 'Tax calculation strategy',
            'contactEmail' => 'Email',
            'contactPhoneNumber' => 'Contact phone number',
        ];

        foreach ($stringFields as $field => $messageFieldName) {
            $message = sprintf('%s must not be longer than 255 characters.', $messageFieldName);

            yield [
                [$field => $valueOverStringMax],
                ['propertyPath' => $field, 'message' => $message],
            ];
        }
    }
}
