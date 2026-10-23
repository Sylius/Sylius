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

use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\Routing\RouterInterface;

final class LocalizedRoutesTest extends AbstractWebTestCase
{
    private RouterInterface $router;

    protected function setUp(): void
    {
        self::createClient(['test_case' => 'LocalizedRoutes']);

        $this->router = self::getContainer()->get('router');
    }

    #[Test]
    public function it_matches_the_localized_paths_under_the_route_name(): void
    {
        $attributes = $this->router->match('/fr_FR/connexion');

        $this->assertSame('sylius_shop_login', $attributes['_route']);
        $this->assertSame('fr_FR', $attributes['_locale']);
    }

    #[Test]
    public function it_generates_the_localized_path_of_the_current_locale(): void
    {
        $this->router->getContext()->setParameter('_locale', 'fr_FR');

        $this->assertSame('/fr_FR/connexion', $this->router->generate('sylius_shop_login'));
    }
}
