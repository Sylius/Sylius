<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\ThemeBundle\Synchronizer;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\ThemeBundle\Model\ThemeInterface;
use Sylius\Bundle\ThemeBundle\Synchronizer\ThemeMerger;
use Sylius\Bundle\ThemeBundle\Synchronizer\ThemeMergerInterface;
use Zend\Hydrator\HydratorInterface;

/**
 * @mixin ThemeMerger
 *
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class ThemeMergerSpec extends ObjectBehavior
{
    function let(HydratorInterface $themeHydrator)
    {
        $this->beConstructedWith($themeHydrator);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ThemeBundle\Synchronizer\ThemeMerger');
    }

    function it_implements_theme_merger_interface()
    {
        $this->shouldImplement(ThemeMergerInterface::class);
    }

    function it_merges_two_themes_with_each_other_without_changing_the_id(
        HydratorInterface $themeHydrator,
        ThemeInterface $existingTheme,
        ThemeInterface $loadedTheme
    ) {
        $themeHydrator->extract($loadedTheme)->willReturn([
            'id' => null,
            'name' => 'theme/name',
            'path' => 'another/path'
        ]);

        $themeHydrator
            ->hydrate(
                ['name' => 'theme/name', 'path' => 'another/path'],
                $existingTheme
            )
            ->shouldBeCalled()
            ->willReturn($existingTheme)
        ;

        $this->merge($existingTheme, $loadedTheme)->shouldReturn($existingTheme);
    }
}
