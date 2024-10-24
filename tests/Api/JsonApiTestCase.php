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
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\ExpectationFailedException;
use Sylius\Tests\Api\Utils\HeadersBuilder;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Response;

abstract class JsonApiTestCase extends BaseJsonApiTestCase
{
    public const CONTENT_TYPE_HEADER = ['CONTENT_TYPE' => 'application/ld+json', 'HTTP_ACCEPT' => 'application/ld+json'];

    public const PATCH_CONTENT_TYPE_HEADER = ['CONTENT_TYPE' => 'application/merge-patch+json', 'HTTP_ACCEPT' => 'application/ld+json'];

    public const FILE_CONTENT_TYPE_HEADER = ['CONTENT_TYPE' => 'multipart/form-data', 'HTTP_ACCEPT' => 'application/ld+json'];

    private bool $isAdminContext = false;

    private bool $isShopUserContext = false;

    private ?string $adminUserEmail = null;

    private ?string $shopUserEmail = null;

    /** @var array <string, string> */
    private array $defaultGetHeaders = [];

    /** @var array <string, string> */
    private array $defaultPostHeaders = [];

    /** @var array <string, string> */
    private array $defaultPutHeaders = [];

    /** @var array <string, string> */
    private array $defaultPatchHeaders = [];

    /** @var array <string, string> */
    private array $defaultDeleteHeaders = [];

    /**
     * @param array<array-key, mixed> $data
     */
    public function __construct(?string $name = null, array $data = [], int|string $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $this->dataFixturesPath = __DIR__ . '/DataFixtures/ORM';
        $this->expectedResponsesPath = __DIR__ . '/Responses';
    }

    protected function setUpAdminContext(?string $email = null): void
    {
        $this->isAdminContext = true;
        $this->adminUserEmail = $email;
    }

    protected function setUpShopUserContext(?string $email = null): void
    {
        $this->isShopUserContext = true;
        $this->shopUserEmail = $email;
    }

    protected function disableAdminContext(): void
    {
        $this->isAdminContext = false;
    }

    protected function disableShopUserContext(): void
    {
        $this->isShopUserContext = false;
    }

    protected function setUpDefaultGetHeaders(): void
    {
        $this->defaultGetHeaders = [
            'HTTP_ACCEPT' => 'application/ld+json',
            'CONTENT_TYPE' => 'application/ld+json',
        ];
    }

    protected function setUpDefaultPostHeaders(): void
    {
        $this->defaultPostHeaders = [
            'HTTP_ACCEPT' => 'application/ld+json',
            'CONTENT_TYPE' => 'application/ld+json',
        ];
    }

    protected function setUpDefaultPutHeaders(): void
    {
        $this->defaultPutHeaders = [
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

    protected function setUpDefaultDeleteHeaders(): void
    {
        $this->defaultDeleteHeaders = [
            'HTTP_ACCEPT' => 'application/ld+json',
            'CONTENT_TYPE' => 'application/ld+json',
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
     * @param array<string, mixed> $body
     */
    protected function requestPost(
        string $uri,
        ?array $body = null,
        array $queryParameters = [],
        array $parameters = [],
        array $headers = [],
        array $files = [],
    ): Crawler
    {
        if (!empty($this->defaultPostHeaders)) {
            $headers = array_merge($this->defaultPostHeaders, $headers);
        }

        return $this->request('POST', $uri, $queryParameters, $headers, $body, $parameters, $files);
    }

    /**
     * @param array<string, array<string>|string> $queryParameters
     * @param array<string, string> $headers
     * @param array<string, mixed> $body
     */
    protected function requestPut(string $uri, ?array $body = null, array $queryParameters = [], array $headers = []): Crawler
    {
        if (!empty($this->defaultPutHeaders)) {
            $headers = array_merge($this->defaultPutHeaders, $headers);
        }

        return $this->request('PUT', $uri, $queryParameters, $headers, $body);
    }

    /**
     * @param array<string, mixed> $body
     * @param array<string, string> $headers
     * @param array<string, array<string>|string> $queryParameters
     */
    protected function requestPatch(string $uri, ?array $body = null, array $queryParameters = [], array $headers = []): Crawler
    {
        if (!empty($this->defaultPatchHeaders)) {
            $headers = array_merge($this->defaultPatchHeaders, $headers);
        }

        return $this->request('PATCH', $uri, $queryParameters, $headers, $body);
    }

    /**
     * @param array<string, array<string>|string> $queryParameters
     * @param array<string, string> $headers
     */
    protected function requestDelete(string $uri, array $queryParameters = [], array $headers = []): Crawler
    {
        if (!empty($this->defaultDeleteHeaders)) {
            $headers = array_merge($this->defaultDeleteHeaders, $headers);
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
    protected function assertResponseCreated(string $filename): void
    {
        $this->assertResponse(
            $this->client->getResponse(),
            $filename,
            Response::HTTP_CREATED,
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

    /** @throws \Exception */
    protected function assertResponseNotFound(string $message = 'Not Found'): void
    {
        $this->assertResponseErrorMessage($message, Response::HTTP_NOT_FOUND);
    }

    /** @throws \Exception */
    protected function assertResponseForbidden(): void
    {
        $this->assertResponseErrorMessage('Access Denied.', Response::HTTP_FORBIDDEN);
    }

    /** @throws \Exception */
    protected function assertResponseErrorMessage(string $message, int $code = Response::HTTP_UNPROCESSABLE_ENTITY): void
    {
        $content = json_decode($this->client->getResponse()->getContent(), true);
        Assert::assertIsArray($content, 'Response content supposed to be an array');

        $expectedContent = [
            '@context' => '/api/v2/contexts/Error',
            '@type' => 'hydra:Error',
            'hydra:title' => 'An error occurred',
            'hydra:description' => $message,
        ];

        Assert::assertSame($expectedContent, $content);
        $this->assertResponseCode($this->client->getResponse(), $code);
    }

    /**
     * @param array<array-key, mixed> $expectedViolations
     * @throws \Exception
     *
     */
    protected function assertResponseViolations(array $expectedViolations, bool $assertViolationsCount = true): void
    {
        $response = $this->client->getResponse();

        if (isset($_SERVER['OPEN_ERROR_IN_BROWSER']) && true === $_SERVER['OPEN_ERROR_IN_BROWSER']) {
            $this->showErrorInBrowserIfOccurred($response);
        }

        $this->assertResponseCode($response, Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->assertJsonHeader($response);

        $assertViolationsCount ?
            $this->assertResponseExactViolations($expectedViolations) :
            $this->assertJsonResponseContainsViolations($response, $expectedViolations)
        ;
    }

    /**
     * @throws \Exception
     *
     * @param array<array-key, mixed> $expectedViolations
     */
    protected function assertResponseExactViolations(array $expectedViolations): void
    {
        $response = $this->client->getResponse();

        $responseContent = $response->getContent() ?: '';
        $this->assertNotEmpty($responseContent);

        $actualViolations = json_decode($responseContent, true)['violations'];
        $actualDescription = json_decode($responseContent, true)['hydra:description'];

        array_walk($actualViolations, function (&$item) {
            unset($item['code']);
        });

        $mappedViolations = array_map(function ($violation) {
            if (empty($violation['propertyPath'])) {
                return $violation['message'];
            }

            return $violation['propertyPath'] . ': ' . $violation['message'];
        }, $expectedViolations);

        $expectedDescription = implode("\n", $mappedViolations);

        $expected = [
            '@context' => '/api/v2/contexts/ConstraintViolationList',
            '@type' => 'ConstraintViolationList',
            'hydra:title' => 'An error occurred',
            'hydra:description' => $expectedDescription,
            'violations' => $expectedViolations,
        ];

        $actual = [
            '@context' => '/api/v2/contexts/ConstraintViolationList',
            '@type' => 'ConstraintViolationList',
            'hydra:title' => 'An error occurred',
            'hydra:description' => $actualDescription,
            'violations' => $actualViolations,
        ];

        $this->assertSame($expected, $actual);
    }

    /**
     * @throws \Exception
     *
     * @param array<array-key, mixed> $expectedViolations
     */
    private function assertJsonResponseContainsViolations(
        Response $response,
        array $expectedViolations,
    ): void {
        $responseContent = $response->getContent() ?: '';
        $this->assertNotEmpty($responseContent);
        $violations = json_decode($responseContent, true)['violations'] ?? [];

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
    protected function request(
        string $method,
        string $uri,
        array $queryParameters = [],
        array $headers = [],
        ?array $body = null,
        array $parameters = [],
        array $files = [],
    ): Crawler {
        if ($this->isAdminContext) {
            $email = $this->adminUserEmail ?? 'api@example.com';
            $headers = array_merge($this->headerBuilder()->withAdminUserAuthorization($email)->build(), $headers);
        }

        if ($this->isShopUserContext) {
            $email = $this->shopUserEmail ?? 'shop@example.com';
            $headers = array_merge($this->headerBuilder()->withShopUserAuthorization($email)->build(), $headers);
        }

        $queryStrings = empty($queryParameters) ? '' : http_build_query($queryParameters);

        $uri = $queryStrings ? $uri . '?' . $queryStrings : $uri;

        return $this->client->request(
            method: $method,
            uri: $uri,
            parameters: $parameters,
            files: $files,
            server: $headers,
            content: is_array($body) ? json_encode($body, \JSON_THROW_ON_ERROR) : null,
        );
    }

    protected function assertResponse(Response $response, string $filename, int $statusCode = 200): void
    {
        if (isset($_SERVER['OPEN_ERROR_IN_BROWSER']) && true === $_SERVER['OPEN_ERROR_IN_BROWSER']) {
            $this->showErrorInBrowserIfOccurred($response);
        }

        self::assertEquals(
            $statusCode,
            $response->getStatusCode(),
            json_encode(json_decode($response->getContent(), true), JSON_PRETTY_PRINT)
        );
        $this->assertJsonHeader($response);
        $this->assertJsonResponseContent($response, $filename);
    }

    protected function assertJsonResponseContent(Response $response, string $filename): void
    {
        $expectedFilePath = $this->expectedResponsesPath . '/' . $filename . '.json';
        $expected = file_get_contents($expectedFilePath);
        $actual = $response->getContent() ?: '';

        $expectedArray = json_decode($expected, true);
        if (null === $expectedArray) {
            $this->fail(sprintf('Expected response content is not a valid JSON, check the file: "%s".json', $filename));
        }

        $actualArray = json_decode($actual, true);
        if (null === $actualArray) {
            $this->fail('Actual response content is not a valid JSON');
        }

        $actualArray = $this->replaceDynamicValues($expectedArray, $actualArray);

        try {
            $this->assertJsonStringEqualsJsonString(json_encode($expectedArray), json_encode($actualArray));
        } catch (ExpectationFailedException $e) {
            $expectedFileReferenceMessage = "Check the expected response file: \n" . $expectedFilePath;
            $this->fail($e->getComparisonFailure()->getDiff() . "\n" . $expectedFileReferenceMessage);
        }
    }

    protected function replaceDynamicValues(array $expectedArray, array $actualArray): array
    {
        foreach ($expectedArray as $key => $value) {
            if (is_array($value) && isset($actualArray[$key]) && is_array($actualArray[$key])) {
                $actualArray[$key] = $this->replaceDynamicValues($value, $actualArray[$key]);

                continue;
            }

            if (is_string($value)) {
                if (str_contains($value, '@integer@')) {
                    if (!isset($actualArray[$key])) {
                        continue;
                    }
                    $position = strpos($value, '@integer@');
                    if (strcmp(substr($value, 0, $position), substr((string) $actualArray[$key], 0, $position)) !== 0) {
                        continue;
                    }
                    $actualArray[$key] = substr_replace((string)$actualArray[$key], '@integer@', $position);
                }
                elseif (str_contains($value, '@array@')) {
                    $position = strpos($value, '@array@');
                    $actualArray[$key] = substr_replace(json_encode($actualArray[$key]), '@array@', $position);
                }
                elseif (str_contains($value, '@boolean@')) {
                    $position = strpos($value, '@boolean@');
                    $actualArray[$key] = substr_replace((string)$actualArray[$key], '@boolean@', $position);
                }
                elseif (str_contains($value, '@string@')) {
                    if (!isset($actualArray[$key])) {
                        continue;
                    }
                    $position = strpos($value, '@string@');
                    if (strcmp(substr($value, 0, $position), substr((string) $actualArray[$key], 0, $position)) !== 0) {
                        continue;
                    }
                    if (!str_contains($actualArray[$key], substr($value, 8))) {
                        continue;
                    }

                    $position = strpos($value, '@string@');
                    $limit = null;
                    if (strlen($value) > strlen('@string@')) {
                        $expectedRightSubstring = substr($value, $position + strlen('@string@'));
                        $actualRightSubstringPosition = strpos($actualArray[$key], $expectedRightSubstring);
                        if (false !== $actualRightSubstringPosition) {
                            $limit = $actualRightSubstringPosition - $position;
                        }
                    }
                    $actualArray[$key] = substr_replace((string)$actualArray[$key], '@string@', $position, $limit);
                } elseif (str_contains($value, '@date@')) {
                    $position = strpos($value, '@date@');
                    $actualArray[$key] = substr_replace((string)$actualArray[$key], '@date@', $position);
                }
            }
        }

        return $actualArray;
    }
}
