<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\Component\Core\Translation;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Translation\TranslatableEntityLocaleAssigner;
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Sylius\Component\Locale\Context\LocaleNotFoundException;
use Sylius\Component\Resource\Model\TranslatableInterface;
use Sylius\Component\Resource\Translation\Provider\TranslationLocaleProviderInterface;
use Sylius\Component\Resource\Translation\TranslatableEntityLocaleAssignerInterface;

/**
 * @author Michał Marcinkowski <michal.marcinkowski@lakion.com>
 */
final class TranslatableEntityLocaleAssignerSpec extends ObjectBehavior
{
    function let(LocaleContextInterface $localeContext, TranslationLocaleProviderInterface $translationLocaleProvider)
    {
        $this->beConstructedWith($localeContext, $translationLocaleProvider);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(TranslatableEntityLocaleAssigner::class);
    }

    function it_implements_traslatable_entity_locale_assigner_interface()
    {
        $this->shouldImplement(TranslatableEntityLocaleAssignerInterface::class);
    }

    function it_should_assign_current_and_default_locale_to_given_translatable_entity(
        LocaleContextInterface $localeContext,
        TranslationLocaleProviderInterface $translationLocaleProvider,
        TranslatableInterface $translatableEntity
    )
    {
        $localeContext->getLocaleCode()->willReturn('de_DE');
        $translationLocaleProvider->getDefaultLocaleCode()->willReturn('en_US');

        $translatableEntity->setCurrentLocale('de_DE')->shouldBeCalled();
        $translatableEntity->setFallbackLocale('en_US')->shouldBeCalled();

        $this->assignLocale($translatableEntity);
    }

    function it_should_use_default_locale_as_current_if_could_not_resolve_the_current_locale(
        LocaleContextInterface $localeContext,
        TranslationLocaleProviderInterface $translationLocaleProvider,
        TranslatableInterface $translatableEntity
    )
    {
        $localeContext->getLocaleCode()->willThrow(new LocaleNotFoundException());
        $translationLocaleProvider->getDefaultLocaleCode()->willReturn('en_US');

        $translatableEntity->setCurrentLocale('en_US')->shouldBeCalled();
        $translatableEntity->setFallbackLocale('en_US')->shouldBeCalled();

        $this->assignLocale($translatableEntity);
    }
}
