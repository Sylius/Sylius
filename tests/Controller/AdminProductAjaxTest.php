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

use Exception;

final class AdminProductAjaxTest extends SessionAwareAjaxTestCase
{
    /**
     * @test
     *
     * @throws Exception
     */
    public function it_denies_access_to_a_products_list_for_not_authenticated_user()
    {
        $this->client->request('GET', '/admin/ajax/products/');

        $response = $this->client->getResponse();
        $this->assertTrue($response->isRedirection());
    }
}
