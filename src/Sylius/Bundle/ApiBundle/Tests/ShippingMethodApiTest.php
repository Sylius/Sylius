<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ApiBundle\Tests;

use Sylius\Bundle\ApiBundle\Test\ApiTestCase;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class ShippingMethodApiTest extends ApiTestCase
{
    public function testGetShippingMethod()
    {
        $method = $this->getLastResource('shipping_method');

        $client = static::createClient();
        $client->request('GET', '/api/shipping-methods/'.$method->getId());

        $response = $client->getResponse();

        $this->assertJsonResponse($response);
        $this->assertJsonResponseContent($response, 'get_shipping_method');
    }

    public function testGetShippingMethods()
    {
        $client = static::createClient();
        $client->request('GET', '/api/shipping-methods/');

        $response = $client->getResponse();

        $this->assertJsonResponse($response);
        $this->assertJsonResponseContent($response, 'get_shipping_methods');
    }

    public function testCreateShippingMethod()
    {
        $client = static::createClient();
        $client->request('POST', '/api/shipping-methods/', array('name' => 'Super Shipping', 'configuration' => array('amount' => 25.00)));

        $response = $client->getResponse();

        $this->assertEquals(201, $response->getStatusCode());
        $this->assertEquals('Super Shipping', $this->getLastResource('shipping_method')->getName());
    }

    public function testValidationForCreatingShippingMethod()
    {
        $client = static::createClient();
        $client->request('POST', '/api/shipping-methods/', array('description' => 'Really nice method without required name'));

        $response = $client->getResponse();

        $this->assertEquals(400, $response->getStatusCode());
    }

    public function testUpdateShippingMethod()
    {
        $method = $this->getLastResource('shipping_method');

        $client = static::createClient();
        $client->request('PUT', '/api/shipping-methods/'.$method->getId(), array('name' => 'Heavy Items'));

        $response = $client->getResponse();

        $this->assertEquals(204, $response->getStatusCode());
        $this->assertEquals('Heavy Items', $this->getLastResource('shipping_method')->getName());
    }

    public function testValidationForUpdatingShippingMethod()
    {
        $method = $this->getLastResource('shipping_method');

        $client = static::createClient();
        $client->request('PUT', '/api/shipping-methods/'.$method->getId(), array('name' => ''));

        $response = $client->getResponse();

        $this->assertEquals(400, $response->getStatusCode());
        $this->assertEquals('Heavy Stuff', $this->getLastResource('shipping_method')->getName());
    }
}
