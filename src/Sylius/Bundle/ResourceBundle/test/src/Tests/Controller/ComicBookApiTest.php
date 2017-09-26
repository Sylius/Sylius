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

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
final class ComicBookApiTest extends JsonApiTestCase
{
    /**
     * @test
     */
    public function it_allows_creating_a_comic_book()
    {
        $data =
<<<EOT
        {
            "title": "Deadpool #1-69",
            "authorFirstName": "Joe",
            "authorLastName": "Kelly"
        }
EOT;

        $this->client->request('POST', '/v1/comic-books/', [], [], ['CONTENT_TYPE' => 'application/json'], $data);
        $response = $this->client->getResponse();
        $this->assertResponse($response, 'comic-books/create_response', Response::HTTP_CREATED);
    }

    /**
     * @test
     */
    public function it_allows_versioned_creating_a_comic_book()
    {
        $data =
<<<EOT
        {
            "title": "Deadpool #1-69",
            "authorFirstName": "Joe",
            "authorLastName": "Kelly"
        }
EOT;

        $this->client->request('POST', '/v1.2/comic-books/', [], [], ['CONTENT_TYPE' => 'application/json'], $data);
        $response = $this->client->getResponse();
        $this->assertResponse($response, 'comic-books/versioned_create_response', Response::HTTP_CREATED);
    }

    /**
     * @test
     */
    public function it_allows_updating_a_comic_book()
    {
        $objects = $this->loadFixturesFromFile('comic_books.yml');

        $data =
<<<EOT
        {
            "title": "Deadpool #1-69",
            "authorFirstName": "Joe",
            "authorLastName": "Kelly"
        }
EOT;

        $this->client->request('PUT', '/v1/comic-books/' . $objects['comic-book1']->getId(), [], [], ['CONTENT_TYPE' => 'application/json'], $data);
        $response = $this->client->getResponse();
        $this->assertResponseCode($response, Response::HTTP_NO_CONTENT);
    }

    /**
     * @test
     */
    public function it_allows_updating_partial_information_about_a_comic_book()
    {
        $objects = $this->loadFixturesFromFile('comic_books.yml');

        $data =
 <<<EOT
        {
            "authorFirstName": "Joe",
            "authorLastName": "Kelly"
        }
EOT;

        $this->client->request('PATCH', '/v1/comic-books/' . $objects['comic-book1']->getId(), [], [], ['CONTENT_TYPE' => 'application/json'], $data);
        $response = $this->client->getResponse();
        $this->assertResponseCode($response, Response::HTTP_NO_CONTENT);
    }

    /**
     * @test
     */
    public function it_allows_removing_a_comic_book()
    {
        $objects = $this->loadFixturesFromFile('comic_books.yml');

        $this->client->request('DELETE', '/v1/comic-books/' . $objects['comic-book1']->getId());
        $response = $this->client->getResponse();
        $this->assertResponseCode($response, Response::HTTP_NO_CONTENT);
    }

    /**
     * @test
     */
    public function it_allows_showing_a_comic_book()
    {
        $objects = $this->loadFixturesFromFile('comic_books.yml');

        $this->client->request('GET', '/v1/comic-books/' . $objects['comic-book1']->getId());
        $response = $this->client->getResponse();
        $this->assertResponse($response, 'comic-books/show_response');
    }

    /**
     * @test
     */
    public function it_allows_versioning_of_a_showing_comic_book_serialization()
    {
        $objects = $this->loadFixturesFromFile('comic_books.yml');

        $this->client->request('GET', '/v1.2/comic-books/' . $objects['comic-book1']->getId());
        $response = $this->client->getResponse();
        $this->assertResponse($response, 'comic-books/versioned_show_response');
    }

    /**
     * @test
     */
    public function it_allows_indexing_of_comic_books()
    {
        $this->loadFixturesFromFile('comic_books.yml');

        $this->client->request('GET', '/v1/comic-books/');
        $response = $this->client->getResponse();
        $this->assertResponse($response, 'comic-books/index_response');
    }

    /**
     * @test
     */
    public function it_allows_versioned_indexing_of_comic_books()
    {
        $this->loadFixturesFromFile('comic_books.yml');

        $this->client->request('GET', '/v1.2/comic-books/');
        $response = $this->client->getResponse();
        $this->assertResponse($response, 'comic-books/versioned_index_response');
    }

    /**
     * @test
     */
    public function it_does_not_allow_showing_resource_if_it_does_not_exist()
    {
        $this->loadFixturesFromFile('comic_books.yml');

        $this->client->request('GET', '/v1/comic-books/3');
        $response = $this->client->getResponse();
        $this->assertResponseCode($response, Response::HTTP_NOT_FOUND);
    }
}
