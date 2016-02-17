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
use Sylius\Component\Resource\Factory\FactoryInterface;

/**
 * @mixin ThemeFactory
 *
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class ThemeFactorySpec extends ObjectBehavior
{
    function let(FactoryInterface $basicThemeFactory)
    {
        $this->beConstructedWith($basicThemeFactory);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ThemeBundle\Factory\ThemeFactory');
    }

    function it_implements_theme_factory_interface()
    {
        $this->shouldImplement(ThemeFactoryInterface::class);
    }

    function it_creates_theme_based_on_array_data(FactoryInterface $basicThemeFactory, ThemeInterface $theme)
    {
        $basicThemeFactory->createNew()->willReturn($theme);

        $theme->setName('example/theme')->shouldBeCalled();
        $theme->setPath('/theme/path')->shouldBeCalled();

        $this->createFromArray(['name' => 'example/theme', 'path' => '/theme/path'])->shouldReturn($theme);
    }

    function it_creates_theme_with_optional_properties_based_on_array_data(
        FactoryInterface $basicThemeFactory,
        ThemeInterface $theme
    ) {
        $basicThemeFactory->createNew()->willReturn($theme);

        $theme->setName('example/theme')->shouldBeCalled();
        $theme->setPath('/theme/path')->shouldBeCalled();

        $theme->setAuthors([['name' => 'Ryszard Rynkowski']])->shouldBeCalled();
        $theme->setTitle('Example theme')->shouldBeCalled();
        $theme->setDescription('The best theme all around the world')->shouldBeCalled();
        $theme->setParentsNames(['example/parent-theme'])->shouldBeCalled();

        $this->createFromArray([
            'name' => 'example/theme',
            'path' => '/theme/path',
            'authors' => [['name' => 'Ryszard Rynkowski']],
            'title' => 'Example theme',
            'description' => 'The best theme all around the world',
            'parents' => ['example/parent-theme'],
        ])->shouldReturn($theme);
    }
}
