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

namespace spec\Sylius\Bundle\ApiBundle\DataPersister;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use ApiPlatform\Core\DataPersister\ResumableDataPersisterInterface;
use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ApiBundle\Exception\TranslationInDefaultLocaleCannotBeRemoved;
use Sylius\Component\Resource\Model\TranslatableInterface;
use Sylius\Component\Resource\Translation\Provider\TranslationLocaleProviderInterface;

final class TranslatableDataPersisterSpec extends ObjectBehavior
{
    function let(TranslationLocaleProviderInterface $localeProvider): void
    {
        $this->beConstructedWith($localeProvider);
    }

    function it_is_a_context_aware_data_persister(): void
    {
        $this->shouldImplement(ContextAwareDataPersisterInterface::class);
    }

    function it_is_a_resumable_data_persister(): void
    {
        $this->shouldImplement(ResumableDataPersisterInterface::class);
    }

    function it_supports_only_translatable(TranslatableInterface $translatable): void
    {
        $this->supports(new \stdClass())->shouldReturn(false);
        $this->supports($translatable)->shouldReturn(true);
    }

    function it_does_nothing_if_there_is_a_translation_in_default_locale(
        TranslationLocaleProviderInterface $localeProvider,
        TranslatableInterface $translatable,
        TranslatableInterface $translation,
    ): void {
        $localeProvider->getDefaultLocaleCode()->willReturn('en_US');
        $translatable->getTranslations()->willReturn(new ArrayCollection(['en_US' => $translation]));

        $this->persist($translatable)->shouldReturn($translatable);
    }

    function it_throws_an_exception_if_there_is_no_translation_in_default_locale(
        TranslationLocaleProviderInterface $localeProvider,
        TranslatableInterface $translatable,
        TranslatableInterface $translation,
    ): void {
        $localeProvider->getDefaultLocaleCode()->willReturn('en_US');
        $translatable->getTranslations()->willReturn(new ArrayCollection(['de_DE' => $translation]));

        $this->shouldThrow(TranslationInDefaultLocaleCannotBeRemoved::class)->during('persist', [$translatable]);
    }

    function it_does_nothing_during_removing_object(TranslatableInterface $translatable): void
    {
        $this->remove($translatable)->shouldReturn($translatable);
    }

    function it_is_resumable(): void
    {
        $this->resumable()->shouldReturn(true);
    }
}
