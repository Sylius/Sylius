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

namespace spec\Sylius\Bundle\ThemeBundle\Factory;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ThemeBundle\Factory\ThemeAuthorFactoryInterface;
use Sylius\Bundle\ThemeBundle\Model\ThemeAuthor;

final class ThemeAuthorFactorySpec extends ObjectBehavior
{
    function it_implements_theme_author_factory_interface(): void
    {
        $this->shouldImplement(ThemeAuthorFactoryInterface::class);
    }

    function it_creates_an_author_from_an_array(): void
    {
        $expectedAuthor = new ThemeAuthor();
        $expectedAuthor->setName('Rynkowsky');
        $expectedAuthor->setEmail('richard@rynkowsky.com');

        $this
            ->createFromArray(['name' => 'Rynkowsky', 'email' => 'richard@rynkowsky.com'])
            ->shouldBeLike($expectedAuthor)
        ;
    }
}
