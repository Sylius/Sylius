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
use Prophecy\Argument;
use Sylius\Bundle\ThemeBundle\Factory\ThemeFactory;
use Sylius\Bundle\ThemeBundle\Factory\ThemeFactoryInterface;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

/**
 * @mixin ThemeFactory
 *
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class ThemeFactorySpec extends ObjectBehavior
{
    function let(
        $themeClassName = 'Sylius\Bundle\ThemeBundle\Model\Theme',
        PropertyAccessorInterface $propertyAccessor
    ) {
        $this->beConstructedWith($themeClassName, $propertyAccessor);
    }
    
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ThemeBundle\Factory\ThemeFactory');
    }

    function it_implements_theme_factory_interface()
    {
        $this->shouldImplement(ThemeFactoryInterface::class);
    }

    function it_creates_theme_from_valid_array($themeClassName, PropertyAccessorInterface $propertyAccessor)
    {
        $data = [
            'name' => 'Foo bar',
            'slug' => 'foo/bar',
        ];

        $propertyAccessor->setValue(Argument::any(), 'name', 'Foo bar')->shouldBeCalled();
        $propertyAccessor->setValue(Argument::any(), 'slug', 'foo/bar')->shouldBeCalled();
        $propertyAccessor->setValue(Argument::any(), 'parentsSlugs', [])->shouldBeCalled();

        $this->createFromArray($data)->shouldHaveType($themeClassName);
    }

    function it_throws_exception_if_given_array_is_invalid()
    {
        $data = [
            'name' => 'Foo bar',
        ];

        $this->shouldThrow('\Exception')->duringCreateFromArray($data);
    }
}
