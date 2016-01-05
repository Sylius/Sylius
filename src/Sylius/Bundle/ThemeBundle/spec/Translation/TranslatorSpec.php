<?php

namespace spec\Sylius\Bundle\ThemeBundle\Translation;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\ThemeBundle\Context\ThemeContextInterface;
use Sylius\Bundle\ThemeBundle\Model\ThemeInterface;
use Sylius\Bundle\ThemeBundle\Translation\Translator;
use Symfony\Component\HttpKernel\CacheWarmer\WarmableInterface;
use Symfony\Component\Translation\MessageCatalogueInterface;
use Symfony\Component\Translation\TranslatorBagInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * @mixin Translator
 *
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class TranslatorSpec extends ObjectBehavior
{
    function let(
        TranslatorInterface $translator,
        ThemeContextInterface $themeContext
    ) {
        $translator->implement(TranslatorBagInterface::class);

        $this->beConstructedWith($translator, $themeContext);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ThemeBundle\Translation\Translator');
    }

    function it_implements_translator_interface()
    {
        $this->shouldImplement(TranslatorInterface::class);
    }

    function it_implements_translator_bag_interface()
    {
        $this->shouldImplement(TranslatorBagInterface::class);
    }

    function it_implements_warmable_interface()
    {
        $this->shouldImplement(WarmableInterface::class);
    }

    function it_proxies_getting_the_locale_to_the_decorated_translator(TranslatorInterface $translator)
    {
        $translator->getLocale()->willReturn('pl_PL');

        $this->getLocale()->shouldReturn('pl_PL');
    }

    function it_proxies_setting_the_locale_to_the_decorated_translator(TranslatorInterface $translator)
    {
        $translator->setLocale('pl_PL')->shouldBeCalled();

        $this->setLocale('pl_PL');
    }

    function it_translates_id_using_theme_translations(
        TranslatorInterface $translator,
        ThemeContextInterface $themeContext,
        ThemeInterface $theme
    ) {
        $theme->getSlug()->willReturn('theme/slug');
        $themeContext->getThemeHierarchy()->willReturn([$theme]);

        $translator->trans('id|theme/slug', Argument::cetera())->willReturn('Theme translation');
        $translator->trans('id', Argument::cetera())->shouldNotBeCalled();

        $this->trans('id')->shouldReturn('Theme translation');
    }

    function it_translates_id_using_default_translations(
        TranslatorInterface $translator,
        ThemeContextInterface $themeContext,
        ThemeInterface $theme
    ) {
        $theme->getSlug()->willReturn('theme/slug');
        $themeContext->getThemeHierarchy()->willReturn([$theme]);

        $translator->trans('id|theme/slug', Argument::cetera())->willReturn('id|theme/slug');
        $translator->trans('id', Argument::cetera())->willReturn('Default translation');

        $this->trans('id')->shouldReturn('Default translation');
    }

    function it_returns_id_if_there_is_no_given_translation(
        TranslatorInterface $translator,
        ThemeContextInterface $themeContext,
        ThemeInterface $theme
    ) {
        $theme->getSlug()->willReturn('theme/slug');
        $themeContext->getThemeHierarchy()->willReturn([$theme]);

        $translator->trans('id|theme/slug', Argument::cetera())->willReturn('id|theme/slug');
        $translator->trans('id', Argument::cetera())->willReturn('id');

        $this->trans('id')->shouldReturn('id');
    }

    function it_choice_translates_id_using_theme_translations(
        TranslatorInterface $translator,
        ThemeContextInterface $themeContext,
        ThemeInterface $theme
    ) {
        $theme->getSlug()->willReturn('theme/slug');
        $themeContext->getThemeHierarchy()->willReturn([$theme]);

        $translator->transChoice('id|theme/slug', 42, Argument::cetera())->willReturn('Theme translation');
        $translator->transChoice('id', 42, Argument::cetera())->shouldNotBeCalled();

        $this->transChoice('id', 42)->shouldReturn('Theme translation');
    }

    function it_choice_translates_id_using_default_translations(
        TranslatorInterface $translator,
        ThemeContextInterface $themeContext,
        ThemeInterface $theme
    ) {
        $theme->getSlug()->willReturn('theme/slug');
        $themeContext->getThemeHierarchy()->willReturn([$theme]);

        $translator->transChoice('id|theme/slug', 42, Argument::cetera())->willReturn('id|theme/slug');
        $translator->transChoice('id', 42, Argument::cetera())->willReturn('Default translation');

        $this->transChoice('id', 42)->shouldReturn('Default translation');
    }

    function it_returns_id_if_there_is_no_given_choice_translation(
        TranslatorInterface $translator,
        ThemeContextInterface $themeContext,
        ThemeInterface $theme
    ) {
        $theme->getSlug()->willReturn('theme/slug');
        $themeContext->getThemeHierarchy()->willReturn([$theme]);

        $translator->transChoice('id|theme/slug', 42, Argument::cetera())->willReturn('id|theme/slug');
        $translator->transChoice('id', 42, Argument::cetera())->willReturn('id');

        $this->transChoice('id', 42)->shouldReturn('id');
    }

    function it_proxies_getting_catalogue_for_given_locale_to_the_decorated_translator(
        TranslatorBagInterface $translator,
        MessageCatalogueInterface $messageCatalogue
    ) {
        $translator->getCatalogue('pl_PL')->willReturn($messageCatalogue);

        $this->getCatalogue('pl_PL')->shouldReturn($messageCatalogue);
    }

    function it_does_not_warm_up_if_decorated_translator_is_not_warmable()
    {
        $this->warmUp('cache');
    }

    function it_warms_up_if_decorated_translator_is_warmable(WarmableInterface $translator)
    {
        $translator->warmUp('cache')->shouldBeCalled();

        $this->warmUp('cache');
    }
}
