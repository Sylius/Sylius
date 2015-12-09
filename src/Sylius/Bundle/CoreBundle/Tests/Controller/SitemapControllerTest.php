<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Tests\Controller;

use Lakion\ApiTestCase\XmlApiTestCase;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
class SitemapControllerTest extends XmlApiTestCase
{
    public function testShowActionResponse()
    {
        $this->purgeDataBase();
        $this->loadFixturesFromFile('Product.yml');
        $this->client->request('GET', '/sitemap.xml');

        $response = $this->client->getResponse();

        $this->assertResponse($response, 'show_sitemap');
    }
}
