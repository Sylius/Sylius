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

namespace Sylius\Tests\Functional\Bundles\LocaleBundle\Context;

use Fidry\AliceDataFixtures\LoaderInterface;
use Fidry\AliceDataFixtures\Persistence\PurgeMode;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Request;

final class LocaleResolvingTest extends KernelTestCase
{
    /** @test */
    public function it_ignores_accept_language_header_when_locale_is_present_in_url(): void
    {
        $this->loadFixtures([
            dirname(__DIR__, 3) . '/fixtures/Bundles/LocaleBundle/Context/LocaleResolving/fixtures.yaml',
        ]);

        $request = Request::create(
            uri: '/en_US/',
            server: [
                'HTTP_ACCEPT_LANGUAGE' => 'pl_PL',
            ],
        );

        $this->bootKernel();
        $kernel = $this->createKernel();

        $response = $kernel->handle($request);
        /** @var string $content */
        $content = $response->getContent();

        $this->assertStringContainsString('Your cart is empty.', $content);
    }

    /** @test */
    public function it_sets_locale_based_on_accept_language_header_when_no_locale_in_url_provided(): void
    {
        $this->loadFixtures([
            dirname(__DIR__, 3) . '/fixtures/Bundles/LocaleBundle/Context/LocaleResolving/fixtures.yaml',
        ]);

        $request = Request::create(
            uri: '/admin/login',
            server: [
                'HTTP_ACCEPT_LANGUAGE' => 'pl_PL',
            ],
        );

        $this->bootKernel();
        $kernel = $this->createKernel();

        $response = $kernel->handle($request);
        /** @var string $content */
        $content = $response->getContent();

        $this->assertStringContainsString('Nazwa użytkownika', $content);
    }

    /** @test */
    public function it_redirects_to_default_locale_when_not_defined_in_the_request_nor_header(): void
    {
        $this->loadFixtures([
            dirname(__DIR__, 3) . '/fixtures/Bundles/LocaleBundle/Context/LocaleResolving/fixtures.yaml',
        ]);

        $request = Request::create(
            uri: '/',
        );

        $this->bootKernel();
        $kernel = $this->createKernel();

        $response = $kernel->handle($request);
        /** @var string $content */
        $content = $response->getContent();

        $this->assertStringContainsString('Redirecting to /en_US/', $content);
    }

    private function loadFixtures(array $fixtureFiles): void
    {
        /** @var LoaderInterface $fixtureLoader */
        $fixtureLoader = self::getContainer()->get('fidry_alice_data_fixtures.loader.doctrine');

        $fixtureLoader->load($fixtureFiles, [], [], PurgeMode::createDeleteMode());
    }
}
