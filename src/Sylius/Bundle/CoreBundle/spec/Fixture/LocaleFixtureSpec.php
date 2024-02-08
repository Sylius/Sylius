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

namespace spec\Sylius\Bundle\CoreBundle\Fixture;

use Doctrine\Persistence\ObjectManager;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\FixturesBundle\Fixture\FixtureInterface;
use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

final class LocaleFixtureSpec extends ObjectBehavior
{
    function let(FactoryInterface $localeFactory, ObjectManager $localeManager): void
    {
        $this->beConstructedWith($localeFactory, $localeManager, 'default_LOCALE');
    }

    function it_is_a_fixture(): void
    {
        $this->shouldImplement(FixtureInterface::class);
    }

    function it_loads_all_provided_locales(
        FactoryInterface $localeFactory,
        ObjectManager $localeManager,
        LocaleInterface $germanLocale,
        LocaleInterface $englishLocale,
    ): void {
        $localeFactory->createNew()->willReturn($englishLocale, $germanLocale);

        $englishLocale->setCode('en_US')->shouldBeCalled();
        $germanLocale->setCode('de_DE')->shouldBeCalled();

        $localeManager->persist($englishLocale)->shouldBeCalled();
        $localeManager->persist($germanLocale)->shouldBeCalled();

        $localeManager->flush()->shouldBeCalledOnce();

        $this->load(['locales' => ['en_US', 'de_DE'], 'load_default_locale' => false]);
    }

    function it_loads_all_provided_locales_and_the_default_one(
        FactoryInterface $localeFactory,
        ObjectManager $localeManager,
        LocaleInterface $defaultLocale,
        LocaleInterface $germanLocale,
        LocaleInterface $englishLocale,
    ): void {
        $localeFactory->createNew()->willReturn($defaultLocale, $englishLocale, $germanLocale);

        $defaultLocale->setCode('default_LOCALE')->shouldBeCalled();
        $englishLocale->setCode('en_US')->shouldBeCalled();
        $germanLocale->setCode('de_DE')->shouldBeCalled();

        $localeManager->persist($defaultLocale)->shouldBeCalled();
        $localeManager->persist($englishLocale)->shouldBeCalled();
        $localeManager->persist($germanLocale)->shouldBeCalled();

        $localeManager->flush()->shouldBeCalledOnce();

        $this->load(['locales' => ['en_US', 'de_DE'], 'load_default_locale' => true]);
    }

    function it_allows_to_load_default_locale_and_specify_it_explicitly(
        FactoryInterface $localeFactory,
        ObjectManager $localeManager,
        LocaleInterface $defaultLocale,
    ): void {
        $localeFactory->createNew()->willReturn($defaultLocale);

        $defaultLocale->setCode('default_LOCALE')->shouldBeCalled();

        $localeManager->persist($defaultLocale)->shouldBeCalledOnce();

        $localeManager->flush()->shouldBeCalledOnce();

        $this->load(['locales' => ['default_LOCALE'], 'load_default_locale' => true]);
    }

    function it_creates_and_persists_default_locale(
        FactoryInterface $localeFactory,
        ObjectManager $localeManager,
        LocaleInterface $defaultLocale,
    ): void {
        $localeFactory->createNew()->willReturn($defaultLocale);

        $defaultLocale->setCode('default_LOCALE')->shouldBeCalled();

        $localeManager->persist($defaultLocale)->shouldBeCalledOnce();

        $localeManager->flush()->shouldBeCalled();

        $this->load(['locales' => [], 'load_default_locale' => true]);
    }

    function it_creates_and_persists_default_locale_and_other_specified_locales(
        FactoryInterface $localeFactory,
        ObjectManager $localeManager,
        LocaleInterface $defaultLocale,
        LocaleInterface $polishLocale,
    ): void {
        $localeFactory->createNew()->willReturn($defaultLocale, $polishLocale);

        $defaultLocale->setCode('default_LOCALE')->shouldBeCalled();
        $polishLocale->setCode('pl_PL')->shouldBeCalled();

        $localeManager->persist($defaultLocale)->shouldBeCalledOnce();
        $localeManager->persist($polishLocale)->shouldBeCalledOnce();

        $localeManager->flush()->shouldBeCalled();

        $this->load(['locales' => ['pl_PL'], 'load_default_locale' => true]);
    }

    function it_deduplicates_passed_locales_and_the_default_one(
        FactoryInterface $localeFactory,
        ObjectManager $localeManager,
        LocaleInterface $defaultLocale,
        LocaleInterface $polishLocale,
    ): void {
        $localeFactory->createNew()->willReturn($defaultLocale, $polishLocale);

        $defaultLocale->setCode('default_LOCALE')->shouldBeCalled();
        $polishLocale->setCode('pl_PL')->shouldBeCalled();

        $localeManager->persist($defaultLocale)->shouldBeCalledOnce();
        $localeManager->persist($polishLocale)->shouldBeCalledOnce();

        $localeManager->flush()->shouldBeCalled();

        $this->load(['locales' => ['pl_PL', 'default_LOCALE', 'pl_PL'], 'load_default_locale' => true]);
    }
}
