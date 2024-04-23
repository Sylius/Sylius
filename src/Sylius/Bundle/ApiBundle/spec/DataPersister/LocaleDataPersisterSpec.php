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
use PhpSpec\ObjectBehavior;
use stdClass;
use Sylius\Bundle\ApiBundle\Exception\LocaleIsUsedException;
use Sylius\Bundle\LocaleBundle\Checker\LocaleUsageCheckerInterface;
use Sylius\Component\Locale\Model\Locale;

final class LocaleDataPersisterSpec extends ObjectBehavior
{
    public function let(
        ContextAwareDataPersisterInterface $decoratedDataPersister,
        LocaleUsageCheckerInterface $localeUsageChecker,
    ): void {
        $this->beConstructedWith($decoratedDataPersister, $localeUsageChecker);
    }

    public function it_supports_only_locale_interface(): void
    {
        $this->supports(new stdClass())->shouldReturn(false);
        $this->supports(new Locale())->shouldReturn(true);
    }

    public function it_persists_locale(
        ContextAwareDataPersisterInterface $decoratedDataPersister,
        Locale $locale,
    ): void {
        $decoratedDataPersister->persist($locale, [])->shouldBeCalled()->willReturn(new stdClass());

        $this->persist($locale);
    }

    public function it_removes_locale(
        ContextAwareDataPersisterInterface $decoratedDataPersister,
        LocaleUsageCheckerInterface $localeUsageChecker,
        Locale $locale,
    ): void {
        $locale->getCode()->willReturn('en_US');

        $localeUsageChecker->isUsed('en_US')->willReturn(false);

        $decoratedDataPersister->remove($locale, [])->shouldBeCalled();

        $this->remove($locale);
    }

    public function it_throws_an_exception_if_locale_is_used(
        ContextAwareDataPersisterInterface $decoratedDataPersister,
        LocaleUsageCheckerInterface $localeUsageChecker,
        Locale $locale,
    ): void {
        $locale->getCode()->willReturn('en_US');

        $localeUsageChecker->isUsed('en_US')->willReturn(true);

        $decoratedDataPersister->remove($locale, [])->shouldNotBeCalled();

        $this
            ->shouldThrow(LocaleIsUsedException::class)
            ->during('remove', [$locale])
        ;
    }
}
