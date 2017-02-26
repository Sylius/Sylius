<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Tests\Controller;

use Lakion\ApiTestCase\JsonApiTestCase;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Order\Model\OrderItemInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
final class ShipmentApiTest extends CheckoutApiTestCase
{
    /**
     * @test
     */
    public function it_denies_getting_a_shipment_for_non_authenticated_user()
    {
        $this->client->request('GET', $this->getShipmentUrl(-1));

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'authentication/access_denied_response', Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @test
     */
    public function it_returns_not_found_response_when_requesting_details_of_a_shipment_which_does_not_exist()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');

        $this->client->request('GET', $this->getShipmentUrl(-1), [], [], static::$authorizedHeaderWithAccept);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'error/not_found_response', Response::HTTP_NOT_FOUND);
    }

    /**
     * @test
     */
    public function it_allows_to_get_shipment()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $this->loadFixturesFromFile('resources/checkout.yml');

        $orderId = $this->prepareOrder();

        $this->client->request('GET', $this->getOrderUrl($orderId), [], [], static::$authorizedHeaderWithAccept);

        $response = $this->client->getResponse();
        $rawResponse = json_decode($response->getContent(), true);

        $this->client->request('GET', $this->getShipmentUrl($rawResponse['shipments'][0]['id']), [], [], static::$authorizedHeaderWithAccept);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'shipment/shipment_show_response', Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_denies_getting_a_collection_of_shipments_for_non_authenticated_user()
    {
        $this->client->request('GET', '/api/v1/shipments/');

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'authentication/access_denied_response', Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @test
     */
    public function it_allows_to_get_a_collection_of_shipments()
    {
        $this->loadFixturesFromFile('authentication/api_administrator.yml');
        $this->loadFixturesFromFile('resources/checkout.yml');

        $this->prepareOrder();

        $this->client->request('GET', '/api/v1/shipments/', [], [], static::$authorizedHeaderWithAccept);

        $response = $this->client->getResponse();
        $this->assertResponse($response, 'shipment/shipment_index_response', Response::HTTP_OK);
    }

    /**
     * @param mixed $orderId
     *
     * @return string
     */
    private function getOrderUrl($orderId)
    {
        return '/api/v1/orders/' . $orderId;
    }

    /**
     * @param mixed $shipmentId
     *
     * @return string
     */
    private function getShipmentUrl($shipmentId)
    {
        return '/api/v1/shipments/' . $shipmentId;
    }
}
