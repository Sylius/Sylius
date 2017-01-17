<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Resource\Translation;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Resource\Model\TranslatableInterface;
use Sylius\Component\Resource\Translation\Provider\TranslationLocaleProviderInterface;
use Sylius\Component\Resource\Translation\TranslatableEntityLocaleAssigner;
use Sylius\Component\Resource\Translation\TranslatableEntityLocaleAssignerInterface;

/**
 * @author Michał Marcinkowski <michal.marcinkowski@lakion.com>
 */
final class TranslatableEntityLocaleAssignerSpec extends ObjectBehavior
{
    function let(TranslationLocaleProviderInterface $translationLocaleProvider)
    {
        $this->beConstructedWith($translationLocaleProvider);
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
        TranslationLocaleProviderInterface $translationLocaleProvider,
        TranslatableInterface $translatableEntity
    )
    {
        $translationLocaleProvider->getDefaultLocaleCode()->willReturn('en_US');

        $translatableEntity->setCurrentLocale('en_US')->shouldBeCalled();
        $translatableEntity->setFallbackLocale('en_US')->shouldBeCalled();

        $this->assignLocale($translatableEntity);
    }
}
