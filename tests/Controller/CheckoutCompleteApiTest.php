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

use Doctrine\ORM\EntityManagerInterface;
use Sylius\Component\Core\Model\ProductVariant;
use Sylius\Component\Product\Model\ProductInterface;
use Sylius\Component\Product\Repository\ProductRepositoryInterface;
use Sylius\Component\Product\Repository\ProductVariantRepositoryInterface;
use Symfony\Component\HttpFoundation\Response;

final class CheckoutCompleteApiTest extends CheckoutApiTestCase
{
    /**
     * @test
     */
    public function it_denies_order_checkout_complete_for_non_authenticated_user()
    {
        $this->client->request('PUT', '/api/v1/checkouts/complete/1');

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'authentication/access_denied_response', Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @test
     */
    public function it_does_not_allow_to_complete_unexisting_order()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');

        $this->client->request('PUT', '/api/v1/checkouts/complete/1', [], [], static::$authorizedHeaderWithContentType);

        $response = $this->client->getResponse();
        $this->assertResponseCode($response, Response::HTTP_NOT_FOUND);
    }

    /**
     * @test
     */
    public function it_does_not_allow_to_complete_order_that_is_not_addressed_and_has_no_shipping_and_payment_method_selected()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $this->loadFixturesFromFile('resources/checkout.yml');

        $cartId = $this->createCart();
        $this->addItemToCart($cartId);
        $this->addressOrder($cartId);
        $this->selectOrderShippingMethod($cartId);

        $this->client->request('PUT', $this->getCheckoutCompleteUrl($cartId), [], [], static::$authorizedHeaderWithContentType);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'checkout/complete_invalid_order_state', Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    /**
     * @test
     */
    public function it_does_not_allow_to_complete_order_with_disabled_product()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $this->loadFixturesFromFile('resources/checkout.yml');

        $cartId = $this->createCart();
        $this->addItemToCart($cartId);
        $this->addressOrder($cartId);
        $this->selectOrderShippingMethod($cartId);
        $this->selectOrderPaymentMethod($cartId);

        /** @var ProductRepositoryInterface $productRepository */
        $productRepository = $this->client->getContainer()->get('sylius.repository.product');
        /** @var ProductInterface $product */
        $product = $productRepository->findOneBy(['code' => 'MUG']);
        $this->assertNotNull($product);
        $product->disable();

        /** @var EntityManagerInterface $productManager */
        $productManager = $this->client->getContainer()->get('sylius.manager.product');
        $productManager->persist($product);
        $productManager->flush();

        $this->client->request('PUT', $this->getCheckoutCompleteUrl($cartId), [], [], static::$authorizedHeaderWithContentType);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'checkout/complete_validation_failed_disabled_product', Response::HTTP_BAD_REQUEST);
    }

    /**
     * @test
     */
    public function it_does_not_allow_to_complete_order_with_insufficient_stock()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $this->loadFixturesFromFile('resources/checkout.yml');

        $cartId = $this->createCart();
        $this->addItemToCart($cartId);
        $this->addressOrder($cartId);
        $this->selectOrderShippingMethod($cartId);
        $this->selectOrderPaymentMethod($cartId);

        /** @var ProductVariantRepositoryInterface $productVariantRepository */
        $productVariantRepository = $this->client->getContainer()->get('sylius.repository.product_variant');
        /** @var ProductVariant $productVariant */
        $productVariant = $productVariantRepository->findOneByCodeAndProductCode('MUG_SW', 'MUG');
        $this->assertNotNull($productVariant);
        $productVariant->setTracked(true);
        $productVariant->setOnHand(0);
        $productVariant->setOnHold(0);

        /** @var EntityManagerInterface $productVariantManager */
        $productVariantManager = $this->client->getContainer()->get('sylius.manager.product_variant');
        $productVariantManager->persist($productVariant);
        $productVariantManager->flush();

        $this->client->request('PUT', $this->getCheckoutCompleteUrl($cartId), [], [], static::$authorizedHeaderWithContentType);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'checkout/complete_validation_failed_insufficient_stock', Response::HTTP_BAD_REQUEST);
    }

    /**
     * @test
     */
    public function it_allows_to_complete_order_that_is_addressed_and_has_shipping_and_payment_method_selected()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $this->loadFixturesFromFile('resources/checkout.yml');

        $cartId = $this->createCart();
        $this->addItemToCart($cartId);
        $this->addressOrder($cartId);
        $this->selectOrderShippingMethod($cartId);
        $this->selectOrderPaymentMethod($cartId);

        $this->client->request('PUT', $this->getCheckoutCompleteUrl($cartId), [], [], static::$authorizedHeaderWithContentType);

        $response = $this->client->getResponse();
        $this->assertResponseCode($response, Response::HTTP_NO_CONTENT);

        $this->client->request('GET', $this->getCheckoutSummaryUrl($cartId), [], [], static::$authorizedHeaderWithAccept);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'checkout/completed_order_response');
    }

    /**
     * @test
     */
    public function it_allows_to_add_a_note_to_order_when_completing()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $this->loadFixturesFromFile('resources/checkout.yml');

        $cartId = $this->createCart();
        $this->addItemToCart($cartId);
        $this->addressOrder($cartId);
        $this->selectOrderShippingMethod($cartId);
        $this->selectOrderPaymentMethod($cartId);

        $data =
<<<EOT
        {
            "notes": "Please, call me before delivery"
        }
EOT;

        $this->client->request('PUT', $this->getCheckoutCompleteUrl($cartId), [], [], static::$authorizedHeaderWithContentType, $data);

        $response = $this->client->getResponse();
        $this->assertResponseCode($response, Response::HTTP_NO_CONTENT);

        $this->client->request('GET', $this->getCheckoutSummaryUrl($cartId), [], [], static::$authorizedHeaderWithAccept);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'checkout/completed_order_response');
    }

    /**
     * @param mixed $cartId
     *
     * @return string
     */
    private function getCheckoutCompleteUrl($cartId)
    {
        return sprintf('/api/v1/checkouts/complete/%d', $cartId);
    }
}
