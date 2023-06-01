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

namespace spec\Sylius\Bundle\ApiBundle\Controller\Locale;

use Doctrine\Persistence\ObjectManager;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\LocaleBundle\Checker\Exception\LocaleIsUsedException;
use Sylius\Bundle\LocaleBundle\Checker\LocaleUsageCheckerInterface;
use Sylius\Component\Locale\Context\LocaleNotFoundException;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

final class DeleteLocaleActionSpec extends ObjectBehavior
{
    function let(
        RepositoryInterface $localeRepository,
        LocaleUsageCheckerInterface $localeUsageChecker,
        ObjectManager $localeManager,
    ): void {
        $this->beConstructedWith($localeRepository, $localeUsageChecker, $localeManager);
    }

    function it_throws_an_exception_when_a_passed_locale_is_used(
        LocaleUsageCheckerInterface $localeUsageChecker,
    ): void {
        $localeUsageChecker->isUsed('en_US')->willReturn(true);

        $this->shouldThrow(LocaleIsUsedException::class)->during('__invoke', ['en_US']);
    }

    function it_throws_an_exception_when_a_passed_locale_does_not_exist(
        RepositoryInterface $localeRepository,
        LocaleUsageCheckerInterface $localeUsageChecker,
    ): void {
        $localeUsageChecker->isUsed('en_US')->willReturn(false);
        $localeRepository->findOneBy(['code' => 'en_US'])->willReturn(null);

        $this->shouldThrow(LocaleNotFoundException::class)->during('__invoke', ['en_US']);
    }

    function it_removes_an_unused_locale(
        RepositoryInterface $localeRepository,
        LocaleUsageCheckerInterface $localeUsageChecker,
        ObjectManager $localeManager,
    ): void {
        $localeUsageChecker->isUsed('en_US')->willReturn(false);
        $localeRepository->findOneBy(['code' => 'en_US'])->willReturn('en_US');

        $localeManager->remove('en_US')->shouldBeCalled();
        $localeManager->flush()->shouldBeCalled();

        $this->__invoke('en_US')->shouldBeLike(new JsonResponse(null, Response::HTTP_NO_CONTENT));
    }
}
