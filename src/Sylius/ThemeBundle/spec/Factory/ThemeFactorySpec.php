<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\ThemeBundle\Factory;

use PhpSpec\ObjectBehavior;
use Sylius\ThemeBundle\Factory\ThemeFactory;
use Sylius\ThemeBundle\Factory\ThemeFactoryInterface;
use Sylius\ThemeBundle\Model\ThemeInterface;

/**
 * @mixin ThemeFactory
 *
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class ThemeFactorySpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\ThemeBundle\Factory\ThemeFactory');
    }

    function it_implements_theme_factory_interface()
    {
        $this->shouldImplement(ThemeFactoryInterface::class);
    }

    function it_creates_a_theme()
    {
        $this->create('example/theme', '/theme/path')->shouldHaveNameAndPath('example/theme', '/theme/path');
    }

    public function getMatchers()
    {
        return [
            'haveNameAndPath' => function (ThemeInterface $theme, $expectedName, $expectedPath) {
                return $expectedName === $theme->getName()
                    && $expectedPath === $theme->getPath()
                ;
            },
        ];
    }
}
