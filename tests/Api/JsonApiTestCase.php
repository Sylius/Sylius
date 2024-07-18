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

namespace Sylius\Tests\Api;

use ApiTestCase\JsonApiTestCase as BaseJsonApiTestCase;
use Sylius\Tests\Api\Utils\AdminUserLoginTrait;
use Sylius\Tests\Api\Utils\HeadersBuilder;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Response;

abstract class JsonApiTestCase extends BaseJsonApiTestCase
{
    use AdminUserLoginTrait;

    public const CONTENT_TYPE_HEADER = ['CONTENT_TYPE' => 'application/ld+json', 'HTTP_ACCEPT' => 'application/ld+json'];

    public const PATCH_CONTENT_TYPE_HEADER = ['CONTENT_TYPE' => 'application/merge-patch+json', 'HTTP_ACCEPT' => 'application/ld+json'];

    public const FILE_CONTENT_TYPE_HEADER = ['CONTENT_TYPE' => 'multipart/form-data', 'HTTP_ACCEPT' => 'application/ld+json'];

    private bool $isAdminContext = false;

    /** @var array <string, string> */
    private array $defaultGetHeaders = [];

    /** @var array <string, string> */
    private array $defaultPatchHeaders = [];

    /**
     * @param array<array-key, mixed> $data
     */
    public function __construct(?string $name = null, array $data = [], int|string $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $this->dataFixturesPath = __DIR__ . '/DataFixtures/ORM';
        $this->expectedResponsesPath = __DIR__ . '/Responses';
    }

    protected function setUpAdminContext(): void
    {
        $this->isAdminContext = true;
    }

    protected function setUpDefaultGetHeaders(): void
    {
        $this->defaultGetHeaders = [
            'HTTP_ACCEPT' => 'application/ld+json',
            'CONTENT_TYPE' => 'application/ld+json',
        ];
    }

    protected function setUpDefaultPatchHeaders(): void
    {
        $this->defaultPatchHeaders = [
            'HTTP_ACCEPT' => 'application/ld+json',
            'CONTENT_TYPE' => 'application/merge-patch+json',
        ];
    }

    protected function get(string $id): ?object
    {
        if (property_exists(static::class, 'container')) {
            return self::$kernel->getContainer()->get($id);
        }

        return parent::get($id);
    }

    protected function getUploadedFile(string $path, string $name, string $type = 'image/jpg'): UploadedFile
    {
        return new UploadedFile(__DIR__ . '/../Resources/' . $path, $name, $type);
    }

    protected function headerBuilder(): HeadersBuilder
    {
        return new HeadersBuilder(
            $this->get('lexik_jwt_authentication.jwt_manager'),
            $this->get('sylius.repository.admin_user'),
            $this->get('sylius.repository.shop_user'),
            self::$kernel->getContainer()->getParameter('sylius.api.authorization_header'),
        );
    }

    /**
     * @param array<string, array<string>|string> $queryParameters
     * @param array<string, string> $headers
     */
    protected function requestGet(string $uri, array $queryParameters = [], array $headers = []): Crawler
    {
        if (!empty($this->defaultGetHeaders)) {
            $headers = array_merge($this->defaultGetHeaders, $headers);
        }

        return $this->request('GET', $uri, $queryParameters, $headers);
    }

    /**
     * @param array<string, array<string>|string> $queryParameters
     * @param array<string, string> $headers
     */
    protected function requestPatch(string $uri, array $queryParameters = [], array $headers = []): Crawler
    {
        if (!empty($this->defaultPatchHeaders)) {
            $headers = array_merge($this->defaultPatchHeaders, $headers);
        }

        return $this->request('PATCH', $uri, $queryParameters, $headers);
    }

    /**
     * @param array<string, array<string>|string> $queryParameters
     * @param array<string, string> $headers
     */
    protected function requestDelete(string $uri, array $queryParameters = [], array $headers = []): Crawler
    {
        if (!empty($this->defaultGetHeaders)) {
            $headers = array_merge($this->defaultGetHeaders, $headers);
        }

        return $this->request('DELETE', $uri, $queryParameters, $headers);
    }

    /** @throws \Exception */
    protected function assertResponseSuccessful(string $filename): void
    {
        $this->assertResponse(
            $this->client->getResponse(),
            $filename,
            Response::HTTP_OK,
        );
    }

    /** @throws \Exception */
    protected function assertResponseUnprocessableEntity(string $filename): void
    {
        $this->assertResponse(
            $this->client->getResponse(),
            $filename,
            Response::HTTP_UNPROCESSABLE_ENTITY,
        );
    }

    /**
     * @throws \Exception
     *
     * @param array<array-key, mixed> $expectedViolations
     */
    protected function assertResponseViolations(Response $response, array $expectedViolations): void
    {
        if (isset($_SERVER['OPEN_ERROR_IN_BROWSER']) && true === $_SERVER['OPEN_ERROR_IN_BROWSER']) {
            $this->showErrorInBrowserIfOccurred($response);
        }

        $this->assertResponseCode($response, Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->assertJsonHeader($response);
        $this->assertJsonResponseViolations($response, $expectedViolations);
    }

    /**
     * @throws \Exception
     *
     * @param array<array-key, mixed> $expectedViolations
     */
    protected function assertJsonResponseViolations(
        Response $response,
        array $expectedViolations,
        bool $assertViolationsCount = true,
    ): void {
        $responseContent = $response->getContent() ?: '';
        $this->assertNotEmpty($responseContent);
        $violations = json_decode($responseContent, true)['violations'] ?? [];

        if ($assertViolationsCount) {
            $this->assertCount(count($expectedViolations), $violations, $responseContent);
        }

        $violationMap = [];
        foreach ($violations as $violation) {
            $violationMap[$violation['propertyPath']][] = $violation['message'];
        }

        foreach ($expectedViolations as $expectedViolation) {
            $propertyPath = $expectedViolation['propertyPath'];
            $this->assertArrayHasKey($propertyPath, $violationMap, $responseContent);
            $this->assertContains($expectedViolation['message'], $violationMap[$propertyPath], $responseContent);
        }
    }

    /**
     * @param array<string, array<string>|string> $queryParameters
     * @param array<string, string> $headers
     */
    protected function request(string $method, string $uri, array $queryParameters = [], array $headers = []): Crawler
    {
        if ($this->isAdminContext) {
            $headers = array_merge($this->headerBuilder()->withAdminUserAuthorization('api@example.com')->build(), $headers);
        }

        $queryStrings = empty($queryParameters) ? '' : http_build_query($queryParameters);

        $uri = $queryStrings ? $uri . '?' . $queryStrings : $uri;

        return $this->client->request(
            method: $method,
            uri: $uri,
            server: $headers,
        );
    }
}
