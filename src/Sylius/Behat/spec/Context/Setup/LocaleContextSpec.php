<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use PhpSpec\ObjectBehavior;
use Sylius\Behat\Context\Setup\LocaleContext;
use Sylius\Component\Core\Test\Services\SharedStorageInterface;
use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @mixin LocaleContext
 *
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
class LocaleContextSpec extends ObjectBehavior
{
    function let(
        SharedStorageInterface $sharedStorage,
        FactoryInterface $localeFactory,
        RepositoryInterface $localeRepository
    ) {
        $this->beConstructedWith($sharedStorage, $localeFactory, $localeRepository);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Behat\Context\Setup\LocaleContext');
    }

    function it_implements_context_interface()
    {
        $this->shouldImplement(Context::class);
    }

    function it_creates_locale(
        FactoryInterface $localeFactory,
        LocaleInterface $locale,
        RepositoryInterface $localeRepository,
        SharedStorageInterface $sharedStorage
    ) {
        $localeFactory->createNew()->willReturn($locale);

        $locale->setCode('no')->shouldBeCalled();
        $sharedStorage->set('locale', $locale)->shouldBeCalled();
        $localeRepository->add($locale)->shouldBeCalled();

        $this->theStoreHasLocale('Norwegian');
    }

    function it_creates_disabled_locale(
        FactoryInterface $localeFactory,
        LocaleInterface $locale,
        RepositoryInterface $localeRepository,
        SharedStorageInterface $sharedStorage
    ) {
        $localeFactory->createNew()->willReturn($locale);

        $locale->setCode('no')->shouldBeCalled();
        $locale->disable()->shouldBeCalled();

        $sharedStorage->set('locale', $locale)->shouldBeCalled();
        $localeRepository->add($locale)->shouldBeCalled();

        $this->theStoreHasDisabledLocale('Norwegian');
    }

    function it_throws_invalid_argument_exception_if_cannot_convert_locale_name_to_code(
        FactoryInterface $localeFactory,
        LocaleInterface $locale,
        RepositoryInterface $localeRepository,
        SharedStorageInterface $sharedStorage
    ) {
        $localeFactory->createNew()->willReturn($locale);
        $locale->setCode('no')->shouldNotBeCalled();
        $sharedStorage->set('locale', $locale)->shouldNotBeCalled();
        $localeRepository->add($locale)->shouldNotBeCalled();

        $this->shouldThrow(\InvalidArgumentException::class)->during('theStoreHasLocale', ['xyz']);
        $this->shouldThrow(\InvalidArgumentException::class)->during('theStoreHasDisabledLocale', ['xyz']);
    }
}
