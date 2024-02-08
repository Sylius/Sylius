<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\Component\Core\Translation;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Checker\CLIContextCheckerInterface;
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Sylius\Component\Locale\Context\LocaleNotFoundException;
use Sylius\Component\Resource\Model\TranslatableInterface;
use Sylius\Component\Resource\Translation\Provider\TranslationLocaleProviderInterface;
use Sylius\Component\Resource\Translation\TranslatableEntityLocaleAssignerInterface;

final class TranslatableEntityLocaleAssignerSpec extends ObjectBehavior
{
    function let(LocaleContextInterface $localeContext, TranslationLocaleProviderInterface $translationLocaleProvider): void
    {
        $this->beConstructedWith($localeContext, $translationLocaleProvider);
    }

    function it_implements_traslatable_entity_locale_assigner_interface(): void
    {
        $this->shouldImplement(TranslatableEntityLocaleAssignerInterface::class);
    }

    function it_should_assign_current_and_default_locale_to_given_translatable_entity(
        LocaleContextInterface $localeContext,
        TranslationLocaleProviderInterface $translationLocaleProvider,
        TranslatableInterface $translatableEntity,
    ): void {
        $localeContext->getLocaleCode()->willReturn('de_DE');
        $translationLocaleProvider->getDefaultLocaleCode()->willReturn('en_US');

        $translatableEntity->setCurrentLocale('de_DE')->shouldBeCalled();
        $translatableEntity->setFallbackLocale('en_US')->shouldBeCalled();

        $this->assignLocale($translatableEntity);
    }

    function it_assigns_locale_if_command_based_context_checker_is_not_provided(
        LocaleContextInterface $localeContext,
        TranslationLocaleProviderInterface $translationLocaleProvider,
        TranslatableInterface $translatableEntity,
        CLIContextCheckerInterface $commandBasedContextChecker,
    ): void {
        $localeContext->getLocaleCode()->willReturn('de_DE');
        $translationLocaleProvider->getDefaultLocaleCode()->willReturn('en_US');

        $translatableEntity->setCurrentLocale('de_DE')->shouldBeCalled();
        $translatableEntity->setFallbackLocale('en_US')->shouldBeCalled();

        $commandBasedContextChecker->isExecutedFromCLI()->shouldNotBeCalled();

        $this->assignLocale($translatableEntity);
    }

    function it_assigns_fallback_locale_if_running_from_command(
        LocaleContextInterface $localeContext,
        TranslationLocaleProviderInterface $translationLocaleProvider,
        TranslatableInterface $translatableEntity,
        CLIContextCheckerInterface $commandBasedContextChecker,
    ): void {
        $this->beConstructedWith($localeContext, $translationLocaleProvider, $commandBasedContextChecker);

        $commandBasedContextChecker->isExecutedFromCLI()->willReturn(true);

        $localeContext->getLocaleCode()->shouldNotBeCalled();
        $translationLocaleProvider->getDefaultLocaleCode()->willReturn('en_US');

        $translatableEntity->setCurrentLocale('en_US')->shouldBeCalled();
        $translatableEntity->setFallbackLocale('en_US')->shouldBeCalled();

        $this->assignLocale($translatableEntity);
    }

    function it_assigns_locale_if_process_is_not_running_from_cli(
        LocaleContextInterface $localeContext,
        TranslationLocaleProviderInterface $translationLocaleProvider,
        TranslatableInterface $translatableEntity,
        CLIContextCheckerInterface $commandBasedContextChecker,
    ): void {
        $this->beConstructedWith($localeContext, $translationLocaleProvider, $commandBasedContextChecker);

        $localeContext->getLocaleCode()->willReturn('de_DE');
        $translationLocaleProvider->getDefaultLocaleCode()->willReturn('en_US');

        $translatableEntity->setCurrentLocale('de_DE')->shouldBeCalled();
        $translatableEntity->setFallbackLocale('en_US')->shouldBeCalled();

        $commandBasedContextChecker->isExecutedFromCLI()->willReturn(false);

        $this->assignLocale($translatableEntity);
    }

    function it_should_use_default_locale_as_current_if_could_not_resolve_the_current_locale(
        LocaleContextInterface $localeContext,
        TranslationLocaleProviderInterface $translationLocaleProvider,
        TranslatableInterface $translatableEntity,
    ): void {
        $localeContext->getLocaleCode()->willThrow(new LocaleNotFoundException());
        $translationLocaleProvider->getDefaultLocaleCode()->willReturn('en_US');

        $translatableEntity->setCurrentLocale('en_US')->shouldBeCalled();
        $translatableEntity->setFallbackLocale('en_US')->shouldBeCalled();

        $this->assignLocale($translatableEntity);
    }
}
