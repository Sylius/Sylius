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

namespace Sylius\Tests\Functional;

use PHPUnit\Framework\Attributes\Before;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use ApiTestCase\JsonApiTestCase;

final class AdminSectionNotFoundPageTest extends JsonApiTestCase
{
    private const ADMIN_404_PAGE_HOOK = 'data-test-back-to-dashboard-link';

    private const SHOP_404_PAGE_HOOK = 'The page you are looking for does not exist.';

    #[Before]
    public function setUpClient(): void
    {
        $this->client = self::createClient(['debug' => false], ['HTTP_ACCEPT' => 'text/html']);
        $this->client->followRedirects();
    }

    
    #[DataProvider('getSyliusResourcesUrlPart')]
    #[Test]
    public function it_shows_admin_not_found_page_for_a_logged_in_admin_when_accessing_nonexistent_resource_edit_page(
        string $syliusResourceUrlPart,
    ): void {
        $this->loginAdminUser();

        $this->client->request('GET', '/admin/' . $syliusResourceUrlPart . '/0/edit');

        $this->assertResponseStatusCodeSame(404);

        $content = $this->client->getResponse()->getContent();
        $this->assertStringContainsString(self::ADMIN_404_PAGE_HOOK, $content);
    }

    #[Test]
    public function it_shows_admin_not_found_page_for_a_logged_in_admin_when_accessing_an_unknown_url(): void
    {
        $this->loginAdminUser();

        $this->client->request('GET', '/admin/this-url-does-not-exist');

        $this->assertResponseStatusCodeSame(404);

        $content = $this->client->getResponse()->getContent();
        $this->assertStringContainsString(self::ADMIN_404_PAGE_HOOK, $content);
    }

    #[Test]
    public function it_shows_shop_not_found_page_for_a_visitor_when_accessing_an_unknown_url(): void
    {
        $this->loadFixtures();

        $this->client->request('GET', '/admin/this-section-does-not-exist');

        $this->assertResponseStatusCodeSame(404);

        $content = $this->client->getResponse()->getContent();
        $this->assertStringContainsString(self::SHOP_404_PAGE_HOOK, $content);
    }

    /** @return iterable<string[]> */
    public static function getSyliusResourcesUrlPart(): iterable
    {
        yield ['users'];
        yield ['catalog-promotions'];
        yield ['channels'];
        yield ['countries'];
        yield ['customers'];
        yield ['customer-groups'];
        yield ['exchange-rates'];
        yield ['locales'];
        yield ['orders'];
        yield ['payment-methods'];
        yield ['products'];
        yield ['product-association-types'];
        yield ['product-attributes'];
        yield ['product-options'];
        yield ['product-reviews'];
        yield ['promotions'];
        yield ['shipping-categories'];
        yield ['shipping-methods'];
        yield ['taxons'];
        yield ['tax-categories'];
        yield ['tax-rates'];
        yield ['zones'];
    }

    private function loginAdminUser(): void
    {
        $this->loadFixtures();

        $this->client->request('GET', '/admin/login');
        $this->client->submitForm('Login', [
            '_username' => 'sylius',
            '_password' => 'sylius',
        ]);
        $afterLoginContent = $this->client->getResponse()->getContent();

        $this->assertStringNotContainsString('Invalid credentials', $afterLoginContent);
    }

    private function loadFixtures(): void
    {
        $this->loadFixturesFromFiles([
            'authentication/administrator.yml',
            'resources/channels.yml',
        ]);
    }
}
