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

namespace spec\Sylius\Bundle\ApiBundle\StateProcessor\Admin\Locale;

use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Post;
use ApiPlatform\State\ProcessorInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\ApiBundle\Exception\LocaleIsUsedException;
use Sylius\Bundle\LocaleBundle\Checker\LocaleUsageCheckerInterface;
use Sylius\Component\Locale\Model\LocaleInterface;

final class RemoveProcessorSpec extends ObjectBehavior
{
    function let(
        ProcessorInterface $removeProcessor,
        LocaleUsageCheckerInterface $localeUsageChecker,
    ): void {
        $this->beConstructedWith($removeProcessor, $localeUsageChecker);
    }

    function it_throws_an_exception_if_object_is_not_a_locale(
        ProcessorInterface $removeProcessor,
        LocaleUsageCheckerInterface $localeUsageChecker,
    ): void {
        $removeProcessor->process(Argument::cetera())->shouldNotBeCalled();
        $localeUsageChecker->isUsed(Argument::any())->shouldNotBeCalled();

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('process', [new \stdClass(), new Delete()])
        ;
    }

    function it_throws_an_exception_if_operation_is_not_delete(
        ProcessorInterface $removeProcessor,
        LocaleUsageCheckerInterface $localeUsageChecker,
        LocaleInterface $locale,
    ): void {
        $removeProcessor->process(Argument::cetera())->shouldNotBeCalled();
        $localeUsageChecker->isUsed(Argument::any())->shouldNotBeCalled();

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('process', [$locale, new Post()])
        ;
    }

    function it_throws_an_exception_if_a_given_locale_is_used(
        ProcessorInterface $removeProcessor,
        LocaleUsageCheckerInterface $localeUsageChecker,
        LocaleInterface $locale,
    ): void {
        $locale->getCode()->willReturn('pl_PL');
        $localeUsageChecker->isUsed('pl_PL')->willReturn(true);

        $removeProcessor->process(Argument::cetera())->shouldNotBeCalled();

        $this
            ->shouldThrow(LocaleIsUsedException::class)
            ->during('process', [$locale, new Delete()])
        ;
    }

    function it_removes_a_locale(
        ProcessorInterface $removeProcessor,
        LocaleUsageCheckerInterface $localeUsageChecker,
        LocaleInterface $locale,
    ): void {
        $operation = new Delete();

        $locale->getCode()->willReturn('pl_PL');
        $localeUsageChecker->isUsed('pl_PL')->willReturn(false);

        $removeProcessor->process($locale, $operation, [], [])->shouldBeCalled();

        $this->process($locale, $operation);
    }
}
