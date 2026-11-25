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

namespace Tests\Sylius\Bundle\CoreBundle\Routing;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\CoreBundle\Routing\LocalizedRoutesLoader;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

#[CoversClass(LocalizedRoutesLoader::class)]
final class LocalizedRoutesLoaderTest extends TestCase
{
    #[Test]
    public function it_replaces_a_configured_route_with_one_route_per_locale(): void
    {
        $routes = new RouteCollection();
        $routes->add('sylius_shop_login', new Route(
            path: '/{_locale}/login',
            defaults: ['_controller' => 'sylius.controller.security::loginAction'],
        ), 10);

        $localizedRoutes = $this->load($routes, [
            'sylius_shop_login' => [
                'en_US' => '/{_locale}/login',
                'fr_FR' => '/{_locale}/connexion',
            ],
        ]);

        $englishRoute = $localizedRoutes->get('sylius_shop_login.en_US');

        self::assertNotNull($englishRoute);
        self::assertSame('/{_locale}/login', $englishRoute->getPath());
        self::assertSame('en_US', $englishRoute->getDefault('_locale'));
        self::assertSame('en_US', $englishRoute->getRequirement('_locale'));
        self::assertSame('sylius_shop_login', $englishRoute->getDefault('_canonical_route'));
        self::assertSame('sylius.controller.security::loginAction', $englishRoute->getDefault('_controller'));
        self::assertSame(10, $localizedRoutes->getPriority('sylius_shop_login.en_US'));

        $frenchRoute = $localizedRoutes->get('sylius_shop_login.fr_FR');

        self::assertNotNull($frenchRoute);
        self::assertSame('/{_locale}/connexion', $frenchRoute->getPath());
        self::assertSame('fr_FR', $frenchRoute->getDefault('_locale'));
        self::assertSame('fr_FR', $frenchRoute->getRequirement('_locale'));
        self::assertSame('sylius_shop_login', $frenchRoute->getDefault('_canonical_route'));
        self::assertSame('sylius.controller.security::loginAction', $frenchRoute->getDefault('_controller'));
        self::assertSame(10, $localizedRoutes->getPriority('sylius_shop_login.fr_FR'));

        self::assertNull($localizedRoutes->get('sylius_shop_login'));
    }

    #[Test]
    public function it_puts_the_localized_routes_in_place_of_the_route_they_replace(): void
    {
        $routes = new RouteCollection();
        $routes->add('sylius_shop_homepage', new Route('/{_locale}/'));
        $routes->add('sylius_shop_login', new Route('/{_locale}/login'));
        $routes->add('sylius_shop_logout', new Route('/{_locale}/logout'));

        $localizedRoutes = $this->load($routes, [
            'sylius_shop_login' => ['en_US' => '/{_locale}/login', 'fr_FR' => '/{_locale}/connexion'],
        ]);

        self::assertSame(
            ['sylius_shop_homepage', 'sylius_shop_login.en_US', 'sylius_shop_login.fr_FR', 'sylius_shop_logout'],
            array_keys($localizedRoutes->all()),
        );
    }

    #[Test]
    public function it_throws_an_exception_when_a_configured_route_is_not_defined(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Route "sylius_shop_signin" cannot be localized, as it is not defined.');

        $this->load(new RouteCollection(), [
            'sylius_shop_signin' => ['fr_FR' => '/{_locale}/connexion'],
        ]);
    }

    /** @param array<string, array<string, string>> $localizedRoutes */
    private function load(RouteCollection $routes, array $localizedRoutes): RouteCollection
    {
        $decoratedLoader = $this->createStub(LoaderInterface::class);
        $decoratedLoader->method('load')->willReturn($routes);

        $loader = new LocalizedRoutesLoader($decoratedLoader, $localizedRoutes);

        return $loader->load('routing.yaml');
    }
}
