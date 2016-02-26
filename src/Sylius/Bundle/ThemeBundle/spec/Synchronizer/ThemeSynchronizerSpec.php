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
use Sylius\Bundle\ThemeBundle\Loader\ThemeLoaderInterface;
use Sylius\Bundle\ThemeBundle\Model\ThemeInterface;
use Sylius\Bundle\ThemeBundle\Repository\ThemeRepositoryInterface;
use Sylius\Bundle\ThemeBundle\Synchronizer\ThemeSynchronizer;
use Sylius\Bundle\ThemeBundle\Synchronizer\ThemeSynchronizerInterface;
use Sylius\Bundle\ThemeBundle\Synchronizer\ThemeMergerInterface;

/**
 * @mixin ThemeSynchronizer
 *
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class ThemeSynchronizerSpec extends ObjectBehavior
{
    function let(
        ThemeLoaderInterface $themeLoader,
        ThemeRepositoryInterface $themeRepository,
        ThemeMergerInterface $themeMerger
    ) {
        $this->beConstructedWith(
            $themeLoader,
            $themeRepository,
            $themeMerger
        );
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ThemeBundle\Synchronizer\ThemeSynchronizer');
    }

    function it_implements_theme_synchronizer_interface()
    {
        $this->shouldImplement(ThemeSynchronizerInterface::class);
    }

    function it_just_adds_themes_if_they_do_not_exist(
        ThemeLoaderInterface $themeLoader,
        ThemeRepositoryInterface $themeRepository,
        ThemeInterface $theme
    ) {
        $themeRepository->findAll()->willReturn([]);
        $themeLoader->load()->willReturn([$theme]);

        $theme->getName()->willReturn('theme/name');
        $theme->getParents()->willReturn([]);
        $themeRepository->findOneByName('theme/name')->willReturn(null);

        $themeRepository->add($theme)->shouldBeCalled();

        $this->synchronize();
    }

    function it_overrides_existing_theme_by_freshly_loaded_theme(
        ThemeLoaderInterface $themeLoader,
        ThemeRepositoryInterface $themeRepository,
        ThemeMergerInterface $themeMerger,
        ThemeInterface $loadedTheme,
        ThemeInterface $existingTheme
    ) {
        $themeRepository->findAll()->willReturn([]);
        $themeLoader->load()->willReturn([$loadedTheme]);

        $loadedTheme->getName()->willReturn('theme/name');
        $loadedTheme->getParents()->willReturn([]);
        $themeRepository->findOneByName('theme/name')->willReturn($existingTheme);
        $themeMerger->merge($existingTheme, $loadedTheme)->willReturn($existingTheme);

        $themeRepository->add($existingTheme)->shouldBeCalled();
        $themeRepository->add($loadedTheme)->shouldNotBeCalled();

        $this->synchronize();
    }

    function it_removes_not_used_themes(
        ThemeLoaderInterface $themeLoader,
        ThemeRepositoryInterface $themeRepository,
        ThemeInterface $loadedTheme,
        ThemeInterface $existingAbandonedTheme
    ) {
        $themeRepository->findAll()->willReturn([$existingAbandonedTheme]);
        $themeLoader->load()->willReturn([$loadedTheme]);

        $loadedTheme->getName()->willReturn('theme/name');
        $loadedTheme->getParents()->willReturn([]);
        $themeRepository->findOneByName('theme/name')->willReturn(null);

        $themeRepository->add($loadedTheme)->shouldBeCalled();

        $existingAbandonedTheme->getName()->willReturn('abandoned/theme');

        $themeRepository->remove($existingAbandonedTheme)->shouldBeCalled();

        $this->synchronize();
    }

    function it_ensures_cohesion_between_parent_themes(
        ThemeLoaderInterface $themeLoader,
        ThemeRepositoryInterface $themeRepository,
        ThemeMergerInterface $themeMerger,
        ThemeInterface $loadedTheme,
        ThemeInterface $loadedParentTheme,
        ThemeInterface $existingParentTheme
    ) {
        $themeRepository->findAll()->willReturn([$existingParentTheme]);
        $existingParentTheme->getName()->willReturn('parent-theme/name');

        $themeLoader->load()->willReturn([$loadedTheme, $loadedParentTheme]);

        $loadedTheme->getName()->willReturn('theme/name');
        $loadedTheme->getParents()->willReturn([$loadedParentTheme]);
        $themeRepository->findOneByName('theme/name')->willReturn(null);

        $loadedParentTheme->getName()->willReturn('parent-theme/name');
        $loadedParentTheme->getParents()->willReturn([]);
        $themeRepository->findOneByName('parent-theme/name')->willReturn($existingParentTheme);

        $loadedTheme->removeParent($loadedParentTheme)->shouldBeCalled();
        $loadedTheme->addParent($existingParentTheme)->shouldBeCalled();

        $themeMerger->merge($existingParentTheme, $loadedParentTheme)->willReturn($existingParentTheme);

        $themeRepository->add($loadedTheme)->shouldBeCalled();
        $themeRepository->add($existingParentTheme)->shouldBeCalled();
        $themeRepository->add($loadedParentTheme)->shouldNotBeCalled();

        $this->synchronize();
    }
}
