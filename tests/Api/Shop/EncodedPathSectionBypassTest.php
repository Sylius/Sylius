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

use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\Attributes\Test;
use Sylius\Tests\Api\JsonApiTestCase;
use Sylius\Tests\Api\Utils\OrderPlacerTrait;

#[AllowMockObjectsWithoutExpectations]
final class EncodedPathSectionBypassTest extends JsonApiTestCase
{
    use OrderPlacerTrait;

    protected function setUp(): void
    {
        $this->setUpDefaultGetHeaders();
        $this->setUpOrderPlacer();

        parent::setUp();
    }

    /**
     * Any path variant the router still resolves to the shop orders collection must go through the
     * same section as the plain path, so that the ShopApiSection-guarded ownership filter applies.
     * A variant the router rejects (404) or redirects (3xx) is safe by itself.
     */
    #[Test]
    public function path_variants_never_bypass_the_shop_user_order_filter(): void
    {
        $this->loadFixturesFromFiles([
            'channel/channel.yaml',
            'cart.yaml',
            'country.yaml',
            'customer.yaml',
            'shipping_method.yaml',
            'payment_method.yaml',
        ]);

        // oliver owns 2 orders, dave owns 1 order
        $this->placeOrder(email: 'oliver@doe.com');
        $this->placeOrder('nAWw2jewpB', 'oliver@doe.com');
        $this->placeOrder('nAWw2jewpC', 'dave@doe.com');

        $headers = $this->headerBuilder()->withShopUserAuthorization('oliver@doe.com')->build();

        $variants = [
            'plain' => '/api/v2/shop/orders',
            'encoded 2' => '/api/v%32/shop/orders',
            'encoded v2' => '/api/%76%32/shop/orders',
            'encoded a in api' => '/%61pi/v2/shop/orders',
            'encoded s in shop' => '/api/v2/%73hop/orders',
            'double slash inside' => '/api/v2//shop/orders',
            'double slash leading' => '//api/v2/shop/orders',
            'dot segment' => '/api/v2/./shop/orders',
            'trailing slash' => '/api/v2/shop/orders/',
            'upper case api' => '/API/v2/shop/orders',
        ];

        $leaking = [];

        foreach ($variants as $label => $uri) {
            $this->requestGet(uri: $uri, headers: $headers);

            $response = $this->client->getResponse();
            $total = $this->totalItemsOrNull($response->getContent());

            if ($response->getStatusCode() === 200 && $total !== null && $total !== 2) {
                $leaking[] = sprintf('%s (%s) returned %d orders instead of 2', $label, $uri, $total);
            }
        }

        self::assertSame([], $leaking, "Path variants leaked other customers' orders:\n" . implode("\n", $leaking));
    }

    /**
     * The shop ownership filters also run as item extensions, and the only other guard on these
     * write operations is SYLIUS_SHOP_USER, which does not look at the subject. A path variant
     * that bypasses the section must therefore not be able to write to another customer's data.
     */
    #[Test]
    public function path_variants_never_allow_writing_to_another_customers_data(): void
    {
        $fixtures = $this->loadFixturesFromFiles(['address_with_customer.yaml', 'country.yaml']);

        $victimAddressId = $fixtures['address_tony']->getId();
        $victimCustomerId = $fixtures['customer_tony']->getId();

        $headers = $this->headerBuilder()
            ->withShopUserAuthorization('oliver@example.com')
            ->withJsonLdContentType()
            ->build()
        ;

        $fullAddress = [
            'firstName' => 'Taken',
            'lastName' => 'Over',
            'street' => 'Attacker Street',
            'countryCode' => 'US',
            'city' => 'Attacker City',
            'postcode' => '11111',
            'provinceCode' => 'US-WY',
        ];

        $probes = [
            ['plain PUT address', 'PUT', '/api/v2/shop/addresses/' . $victimAddressId, $fullAddress],
            ['encoded PUT address', 'PUT', '/api/v%32/shop/addresses/' . $victimAddressId, $fullAddress],
            ['encoded PUT customer', 'PUT', '/api/v%32/shop/customers/' . $victimCustomerId, ['firstName' => 'Taken']],
            ['plain DELETE address', 'DELETE', '/api/v2/shop/addresses/' . $victimAddressId, null],
            ['encoded DELETE address', 'DELETE', '/api/v%32/shop/addresses/' . $victimAddressId, null],
        ];

        $accepted = [];

        foreach ($probes as [$label, $method, $uri, $payload]) {
            $this->client->request(
                method: $method,
                uri: $uri,
                server: $headers,
                content: $payload === null ? null : json_encode($payload, \JSON_THROW_ON_ERROR),
            );

            $status = $this->client->getResponse()->getStatusCode();

            if ($status >= 200 && $status < 300) {
                $accepted[] = sprintf('%s (%s) was accepted with %d', $label, $uri, $status);
            }
        }

        self::assertSame(
            [],
            $accepted,
            "Writes to another customer's data were accepted:\n" . implode("\n", $accepted),
        );
    }

    private function totalItemsOrNull(false|string $content): ?int
    {
        if ($content === false || $content === '') {
            return null;
        }

        $data = json_decode($content, true);
        if (!is_array($data)) {
            return null;
        }

        $total = $data['hydra:totalItems'] ?? $data['totalItems'] ?? null;

        return $total === null ? null : (int) $total;
    }
}
