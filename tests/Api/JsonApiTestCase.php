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
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Response;

abstract class JsonApiTestCase extends BaseJsonApiTestCase
{
    use AdminUserLoginTrait;

    public const CONTENT_TYPE_HEADER = ['CONTENT_TYPE' => 'application/ld+json', 'HTTP_ACCEPT' => 'application/ld+json'];

    public const PATCH_CONTENT_TYPE_HEADER = ['CONTENT_TYPE' => 'application/merge-patch+json', 'HTTP_ACCEPT' => 'application/ld+json'];

    public function __construct(?string $name = null, array $data = [], int|string $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $this->dataFixturesPath = __DIR__ . '/DataFixtures/ORM';
        $this->expectedResponsesPath = __DIR__ . '/Responses/Expected';
    }

    protected function get($id): ?object
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

    protected function assertResponseViolations(Response $response, array $expectedViolations): void
    {
        $violations = json_decode($response->getContent(), true)['violations'] ?? [];
        $this->assertCount(count($expectedViolations), $violations, 'Expected number of violations does not match.');

        $violationMap = [];
        foreach ($violations as $violation) {
            $violationMap[$violation['propertyPath']][] = $violation['message'];
        }

        foreach ($expectedViolations as $expectedViolation) {
            $propertyPath = $expectedViolation['propertyPath'];
            $this->assertArrayHasKey(
                $propertyPath,
                $violationMap,
                sprintf('Property path "%s" not found.', $propertyPath)
            );
            $this->assertContains(
                $expectedViolation['message'],
                $violationMap[$propertyPath],
                sprintf('Message "%s" not found for property path "%s".', $expectedViolation['message'], $propertyPath)
            );
        }
    }
}
