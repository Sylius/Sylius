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

use Doctrine\Common\Collections\Collection;
use Sylius\Bundle\ThemeBundle\PhpSpec\FixtureAwareObjectBehavior;
use Sylius\Bundle\ThemeBundle\Model\ThemeInterface;
use Sylius\Bundle\ThemeBundle\Translation\Loader\Loader;
use Symfony\Component\Translation\Loader\LoaderInterface;
use Symfony\Component\Translation\MessageCatalogueInterface;

/**
 * @mixin Loader
 *
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class LoaderSpec extends FixtureAwareObjectBehavior
{
    function let(LoaderInterface $loader, Collection $resourcesToThemes, ThemeInterface $theme)
    {
        $theme->getLogicalName()->willReturn("sylius/sample-theme");

        $resourcesToThemes->get(realpath($this->getThemeTranslationResourcePath()))->willReturn($theme);
        $resourcesToThemes->get(realpath($this->getVanillaTranslationResourcePath()))->willReturn(null);

        $this->beConstructedWith($loader, $resourcesToThemes);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ThemeBundle\Translation\Loader\Loader');
    }

    function it_implements_translation_loader_interface()
    {
        $this->shouldImplement('Symfony\Component\Translation\Loader\LoaderInterface');
    }

    function it_does_not_change_anything_if_given_file_is_not_included_in_any_theme(
        LoaderInterface $loader,
        MessageCatalogueInterface $messageCatalogue
    ) {
        $loader->load($this->getVanillaTranslationResourcePath(), 'en', 'messages')->shouldBeCalled()->willReturn($messageCatalogue);

        $this->load($this->getVanillaTranslationResourcePath(), 'en', 'messages')->shouldReturn($messageCatalogue);
    }

    function it_adds_theme_logical_name_to_keys_if_given_file_is_included_in_theme(
        LoaderInterface $loader,
        MessageCatalogueInterface $messageCatalogue
    ) {
        $loader->load($this->getThemeTranslationResourcePath(), 'en', 'messages')->shouldBeCalled()->willReturn($messageCatalogue);

        $messagesBefore = ["key" => "value"];
        $messagesAfter = ["key|sylius/sample-theme" => "value"];

        $messageCatalogue->all('messages')->shouldBeCalled()->willReturn($messagesBefore);
        $messageCatalogue->replace($messagesAfter, 'messages')->shouldBeCalled();

        $this->load($this->getThemeTranslationResourcePath(), 'en', 'messages')->shouldReturn($messageCatalogue);
    }

    /**
     * @return string
     */
    private function getThemeTranslationResourcePath()
    {
        return $this->getFixturePath('themes/SampleTheme/translations/messages.en.yml');
    }

    /**
     * @return string
     */
    private function getVanillaTranslationResourcePath()
    {
        return $this->getFixturePath('app/translations/messages.en.yml');
    }
}