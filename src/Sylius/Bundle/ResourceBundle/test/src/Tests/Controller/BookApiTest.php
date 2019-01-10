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
/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AppBundle\Tests\Controller;

use Lakion\ApiTestCase\JsonApiTestCase;
use Symfony\Component\HttpFoundation\Response;

class BookApiTest extends JsonApiTestCase
{
    /**
     * @test
     */
    public function it_allows_creating_a_book()
    {
        $data =
<<<EOT
        {
            "translations": {
                "en_US": {
                    "title": "Star Wars: Dark Disciple"
                }
            },
            "author": "Christie Golden"
        }
EOT;

        $this->client->request('POST', '/books/', [], [], ['CONTENT_TYPE' => 'application/json'], $data);
        $response = $this->client->getResponse();
        $this->assertResponse($response, 'books/create_response', Response::HTTP_CREATED);
    }

    /**
     * @test
     */
    public function it_allows_updating_a_book()
    {
        $objects = $this->loadFixturesFromFile('books.yml');

        $data =
<<<EOT
        {
             "translations": {
                "en_US": {
                    "title": "Star Wars: Dark Disciple"
                },
                "pl_PL": {
                    "title": "Gwiezdne Wojny: Mroczny Uczeń"
                }
            },
            "author": "Christie Golden"
        }
EOT;

        $this->client->request('PUT', '/books/' . $objects['book1']->getId(), [], [], ['CONTENT_TYPE' => 'application/json'], $data);
        $response = $this->client->getResponse();
        $this->assertResponseCode($response, Response::HTTP_NO_CONTENT);
    }

    /**
     * @test
     */
    public function it_allows_updating_partial_information_about_a_book()
    {
        $objects = $this->loadFixturesFromFile('books.yml');

        $data =
 <<<EOT
        {
            "author": "Christie Golden"
        }
EOT;

        $this->client->request('PATCH', '/books/' . $objects['book1']->getId(), [], [], ['CONTENT_TYPE' => 'application/json'], $data);
        $response = $this->client->getResponse();
        $this->assertResponseCode($response, Response::HTTP_NO_CONTENT);
    }

    /**
     * @test
     */
    public function it_allows_removing_a_book()
    {
        $objects = $this->loadFixturesFromFile('books.yml');

        $this->client->request('DELETE', '/books/' . $objects['book1']->getId());
        $response = $this->client->getResponse();
        $this->assertResponseCode($response, Response::HTTP_NO_CONTENT);
    }

    /**
     * @test
     */
    public function it_allows_showing_a_book()
    {
        $objects = $this->loadFixturesFromFile('books.yml');

        $this->client->request('GET', '/books/' . $objects['book1']->getId());
        $response = $this->client->getResponse();
        $this->assertResponse($response, 'books/show_response');
    }

    /**
     * @test
     */
    public function it_allows_indexing_books()
    {
        $this->loadFixturesFromFile('books.yml');

        $this->client->request('GET', '/books/');
        $response = $this->client->getResponse();
        $this->assertResponse($response, 'books/index_response');
    }

    /**
     * @test
     */
    public function it_allows_paginating_the_index_of_books()
    {
        $this->loadFixturesFromFile('more_books.yml');

        $this->client->request('GET', '/books/', ['page' => 2]);
        $response = $this->client->getResponse();
        $this->assertResponse($response, 'books/paginated_index_response');
    }

    /**
     * @test
     */
    public function it_does_not_allow_showing_resource_if_it_not_exists()
    {
        $this->loadFixturesFromFile('books.yml');

        $this->client->request('GET', '/books/3');
        $response = $this->client->getResponse();
        $this->assertResponseCode($response, Response::HTTP_NOT_FOUND);
    }

    /**
     * @test
     */
    public function it_does_not_apply_sorting_for_un_existing_field()
    {
        $this->loadFixturesFromFile('more_books.yml');

        $this->client->request('GET', '/sortable-books/', ['sorting' => ['name' => 'DESC']]);
        $response = $this->client->getResponse();

        $this->assertResponseCode($response, Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_does_not_apply_filtering_for_un_existing_field()
    {
        $this->loadFixturesFromFile('more_books.yml');

        $this->client->request('GET', '/filterable-books/', ['criteria' => ['name' => 'John']]);
        $response = $this->client->getResponse();

        $this->assertResponseCode($response, Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_applies_sorting_for_existing_field()
    {
        $this->loadFixturesFromFile('more_books.yml');

        $this->client->request('GET', '/sortable-books/', ['sorting' => ['id' => 'DESC']]);
        $response = $this->client->getResponse();

        $this->assertResponseCode($response, Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_applies_filtering_for_existing_field()
    {
        $this->loadFixturesFromFile('more_books.yml');

        $this->client->request('GET', '/filterable-books/', ['criteria' => ['author' => 'J.R.R. Tolkien']]);
        $response = $this->client->getResponse();

        $this->assertResponseCode($response, Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function it_allows_creating_a_book_via_custom_factory()
    {
        $data =
            <<<EOT
                    {
            "translations": {
                "en_US": {
                    "title": "Star Wars: Dark Disciple"
                }
            },
            "author": "Christie Golden"
        }
EOT;

        $this->client->request('POST', '/create-custom-book', [], [], ['CONTENT_TYPE' => 'application/json'], $data);
        $response = $this->client->getResponse();
        $this->assertResponse($response, 'books/create_response', Response::HTTP_CREATED);
    }

    /**
     * @test
     */
    public function it_allows_indexing_books_via_custom_repository(): void
    {
        $this->loadFixturesFromFile('books.yml');

        $this->client->request('GET', '/find-custom-books');
        $response = $this->client->getResponse();
        $this->assertResponse($response, 'books/index_response');
    }

    /**
     * @test
     */
    public function it_allows_showing_a_book_via_custom_repository()
    {
        $this->loadFixturesFromFile('books.yml');

        $this->client->request('GET', '/find-custom-book');
        $response = $this->client->getResponse();
        $this->assertResponse($response, 'books/show_response');
    }
}
