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
class ShippingCategoryApiTest extends ApiTestCase
{
    public function testGetShippingCategory()
    {
        $category = $this->getLastResource('shipping_category');

        $client = static::createClient();
        $client->request('GET', '/api/shipping-categories/'.$category->getId());

        $response = $client->getResponse();

        $this->assertJsonResponse($response);
        $this->assertJsonResponseContent($response, 'get_shipping_category');
    }

    public function testGetShippingCategories()
    {
        $client = static::createClient();
        $client->request('GET', '/api/shipping-categories/');

        $response = $client->getResponse();

        $this->assertJsonResponse($response);
        $this->assertJsonResponseContent($response, 'get_shipping_categories');
    }

    public function testCreateShippingCategory()
    {
        $client = static::createClient();
        $client->request('POST', '/api/shipping-categories/', array('name' => 'Boxes'));

        $response = $client->getResponse();

        $this->assertEquals(201, $response->getStatusCode());
        $this->assertEquals('Boxes', $this->getLastResource('shipping_category')->getName());
    }

    public function testValidationForCreatingShippingCategory()
    {
        $client = static::createClient();
        $client->request('POST', '/api/shipping-categories/', array('description' => 'Really nice category without required name'));

        $response = $client->getResponse();

        $this->assertEquals(400, $response->getStatusCode());
    }

    public function testUpdateShippingCategory()
    {
        $category = $this->getLastResource('shipping_category');

        $client = static::createClient();
        $client->request('PUT', '/api/shipping-categories/'.$category->getId(), array('name' => 'Heavy Items'));

        $response = $client->getResponse();

        $this->assertEquals(204, $response->getStatusCode());
        $this->assertEquals('Heavy Items', $this->getLastResource('shipping_category')->getName());
    }

    public function testValidationForUpdatingShippingCategory()
    {
        $category = $this->getLastResource('shipping_category');

        $client = static::createClient();
        $client->request('PUT', '/api/shipping-categories/'.$category->getId(), array('name' => ''));

        $response = $client->getResponse();

        $this->assertEquals(400, $response->getStatusCode());
        $this->assertEquals('Heavy Stuff', $this->getLastResource('shipping_category')->getName());
    }
}
