<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\ThemeBundle\Translation\Loader;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ThemeBundle\Model\ThemeInterface;
use Sylius\Bundle\ThemeBundle\Repository\ThemeRepositoryInterface;
use Sylius\Bundle\ThemeBundle\Translation\Loader\ThemeAwareLoader;
use Symfony\Component\Translation\Loader\LoaderInterface;
use Symfony\Component\Translation\MessageCatalogueInterface;

/**
 * @mixin ThemeAwareLoader
 *
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class ThemeAwareLoaderSpec extends ObjectBehavior
{
    function let(LoaderInterface $loader, ThemeRepositoryInterface $themeRepository)
    {
        $this->beConstructedWith($loader, $themeRepository);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ThemeBundle\Translation\Loader\ThemeAwareLoader');
    }

    function it_implements_translation_loader_interface()
    {
        $this->shouldImplement(LoaderInterface::class);
    }

    function it_does_not_change_anything_if_given_file_is_not_included_in_any_theme(
        LoaderInterface $loader,
        ThemeRepositoryInterface $themeRepository,
        MessageCatalogueInterface $messageCatalogue
    ) {
        $loader->load('/theme/resource.en.xml', 'en', 'messages')->willReturn($messageCatalogue);

        $themeRepository->findOneByPath('/theme/resource.en.xml')->shouldBeCalled()->willReturn(null);

        $this->load('/theme/resource.en.xml', 'en', 'messages')->shouldReturn($messageCatalogue);
    }

    function it_adds_theme_name_to_keys_if_given_file_is_included_in_theme(
        LoaderInterface $loader,
        ThemeRepositoryInterface $themeRepository,
        MessageCatalogueInterface $messageCatalogue,
        ThemeInterface $theme
    ) {
        $loader->load('/theme/resource.en.xml', 'en', 'messages')->willReturn($messageCatalogue);

        $themeRepository->findOneByPath('/theme/resource.en.xml')->willReturn($theme);
        $theme->getName()->willReturn('sylius/sample-theme');

        $messageCatalogue->all('messages')->willReturn(['key' => 'value']);
        $messageCatalogue->replace(['key|sylius/sample-theme' => 'value'], 'messages')->shouldBeCalled();

        $this->load('/theme/resource.en.xml', 'en', 'messages')->shouldReturn($messageCatalogue);
    }
}
