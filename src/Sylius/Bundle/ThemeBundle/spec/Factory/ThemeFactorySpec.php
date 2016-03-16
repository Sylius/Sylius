<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\ThemeBundle\Factory;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ThemeBundle\Factory\ThemeFactory;
use Sylius\Bundle\ThemeBundle\Factory\ThemeFactoryInterface;
use Sylius\Bundle\ThemeBundle\Model\Theme;
use Sylius\Bundle\ThemeBundle\Model\ThemeInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

/**
 * @mixin ThemeFactory
 *
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class ThemeFactorySpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(Theme::class);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ThemeBundle\Factory\ThemeFactory');
    }

    function it_implements_theme_factory_interface()
    {
        $this->shouldImplement(ThemeFactoryInterface::class);
    }

    function it_creates_named_theme()
    {
        $this->createNamed('example/theme')->shouldBeThemeWithName('example/theme');
    }

    public function getMatchers()
    {
        return [
            'beThemeWithName' => function (ThemeInterface $theme, $expectedName) {
                return $expectedName === $theme->getName();
            },
        ];
    }
}
