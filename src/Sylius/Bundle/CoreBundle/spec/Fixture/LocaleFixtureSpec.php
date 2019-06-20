<?php

namespace spec\Sylius\Bundle\CoreBundle\Fixture;

use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Bundle\CoreBundle\Fixture\LocaleFixture;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\FixturesBundle\Fixture\FixtureInterface;
use Sylius\Component\Locale\Model\Locale;
use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

class LocaleFixtureSpec extends ObjectBehavior
{
    function let(FactoryInterface $localeFactory, ObjectManager $localeManager): void
    {
        $this->beConstructedWith($localeFactory, $localeManager, 'default_LOCALE');
    }

    function it_is_a_fixture(): void
    {
        $this->shouldImplement(FixtureInterface::class);
    }

    function it_creates_and_persists_default_locale(
        FactoryInterface $localeFactory,
        ObjectManager $localeManager,
        LocaleInterface $defaultLocale
    ): void {
        $localeFactory->createNew()->willReturn($defaultLocale);

        $defaultLocale->setCode('default_LOCALE')->shouldBeCalled();

        $localeManager->persist($defaultLocale)->shouldBeCalledOnce();

        $localeManager->flush()->shouldBeCalled();

        $this->load(['locales' => []]);
    }

    function it_creates_and_persists_default_locale_and_other_specified_locales(
        FactoryInterface $localeFactory,
        ObjectManager $localeManager,
        LocaleInterface $defaultLocale,
        LocaleInterface $polishLocale
    ): void {
        $localeFactory->createNew()->willReturn($defaultLocale, $polishLocale);

        $defaultLocale->setCode('default_LOCALE')->shouldBeCalled();
        $polishLocale->setCode('pl_PL')->shouldBeCalled();

        $localeManager->persist($defaultLocale)->shouldBeCalledOnce();
        $localeManager->persist($polishLocale)->shouldBeCalledOnce();

        $localeManager->flush()->shouldBeCalled();

        $this->load(['locales' => ['pl_PL']]);
    }

    function it_deduplicates_passed_locales_and_the_default_one(
        FactoryInterface $localeFactory,
        ObjectManager $localeManager,
        LocaleInterface $defaultLocale,
        LocaleInterface $polishLocale
    ): void {
        $localeFactory->createNew()->willReturn($defaultLocale, $polishLocale);

        $defaultLocale->setCode('default_LOCALE')->shouldBeCalled();
        $polishLocale->setCode('pl_PL')->shouldBeCalled();

        $localeManager->persist($defaultLocale)->shouldBeCalledOnce();
        $localeManager->persist($polishLocale)->shouldBeCalledOnce();

        $localeManager->flush()->shouldBeCalled();

        $this->load(['locales' => ['pl_PL', 'default_LOCALE', 'pl_PL']]);
    }
}
