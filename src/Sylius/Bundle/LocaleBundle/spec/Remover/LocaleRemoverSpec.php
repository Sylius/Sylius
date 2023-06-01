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

namespace spec\Sylius\Bundle\LocaleBundle\Remover;

use Doctrine\Persistence\ObjectManager;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\LocaleBundle\Checker\Exception\LocaleIsUsedException;
use Sylius\Bundle\LocaleBundle\Checker\LocaleUsageCheckerInterface;
use Sylius\Component\Locale\Context\LocaleNotFoundException;
use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

final class LocaleRemoverSpec extends ObjectBehavior
{
    function let(
        RepositoryInterface $localeRepository,
        LocaleUsageCheckerInterface $localeUsageChecker,
        ObjectManager $localeManager,
    ): void {
        $this->beConstructedWith($localeRepository, $localeUsageChecker, $localeManager);
    }

    function it_throws_an_exception_when_a_locale_with_passed_locale_code_does_not_exist(
        RepositoryInterface $localeRepository,
    ): void {
        $localeRepository->findOneBy(['code' => 'en_US'])->willReturn(null);

        $this->shouldThrow(LocaleNotFoundException::class)->during('removeByCode', ['en_US']);
    }

    function it_throws_an_exception_when_a_locale_with_passed_locale_id_does_not_exist(
        RepositoryInterface $localeRepository,
    ): void {
        $localeRepository->find(1)->willReturn(null);

        $this->shouldThrow(LocaleNotFoundException::class)->during('removeById', [1]);
    }

    function it_throws_an_exception_when_a_locale_passed_by_code_is_used(
        RepositoryInterface $localeRepository,
        LocaleUsageCheckerInterface $localeUsageChecker,
        ObjectManager $localeManager,
        LocaleInterface $locale,
    ): void {
        $locale->getCode()->willReturn('en_US');
        $localeRepository->findOneBy(['code' => 'en_US'])->willReturn($locale);

        $localeUsageChecker->isUsed('en_US')->willReturn(true);

        $this->shouldThrow(LocaleIsUsedException::class)->during('removeByCode', ['en_US']);
    }

    function it_throws_an_exception_when_a_locale_passed_by_id_is_used(
        RepositoryInterface $localeRepository,
        LocaleUsageCheckerInterface $localeUsageChecker,
        ObjectManager $localeManager,
        LocaleInterface $locale,
    ): void {
        $locale->getCode()->willReturn('en_US');
        $localeRepository->find(1)->willReturn($locale);

        $localeUsageChecker->isUsed('en_US')->willReturn(true);

        $this->shouldThrow(LocaleIsUsedException::class)->during('removeById', [1]);
    }

    function it_removes_an_unused_locale_passed_by_code(
        RepositoryInterface $localeRepository,
        LocaleUsageCheckerInterface $localeUsageChecker,
        ObjectManager $localeManager,
        LocaleInterface $locale,
    ): void {
        $locale->getCode()->willReturn('en_US');
        $localeRepository->findOneBy(['code' => 'en_US'])->willReturn($locale);

        $localeUsageChecker->isUsed('en_US')->willReturn(false);

        $localeManager->remove($locale)->shouldBeCalled();
        $localeManager->flush()->shouldBeCalled();

        $this->removeByCode('en_US');
    }

    function it_removes_an_unused_locale_passed_by_id(
        RepositoryInterface $localeRepository,
        LocaleUsageCheckerInterface $localeUsageChecker,
        ObjectManager $localeManager,
        LocaleInterface $locale,
    ): void {
        $locale->getCode()->willReturn('en_US');
        $localeRepository->find(1)->willReturn($locale);

        $localeUsageChecker->isUsed('en_US')->willReturn(false);

        $localeManager->remove($locale)->shouldBeCalled();
        $localeManager->flush()->shouldBeCalled();

        $this->removeById(1);
    }
}
