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
use Sylius\Bundle\ThemeBundle\Model\ThemeInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class ThemeFactorySpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(ThemeFactory::class);
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
