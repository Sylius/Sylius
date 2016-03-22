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
use Sylius\Component\Core\Test\Services\SharedStorageInterface;
use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
class LocaleContextSpec extends ObjectBehavior
{
    function let(
        FactoryInterface $localeFactory,
        RepositoryInterface $localeRepository,
        SharedStorageInterface $sharedStorage
    ) {
        $this->beConstructedWith($localeFactory, $localeRepository, $sharedStorage);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Behat\Context\Setup\LocaleContext');
    }

    function it_should_implement_context_interface()
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

        $locale->setCode('FR')->shouldBeCalled();
        $sharedStorage->set('locale', $locale)->shouldBeCalled();

    }
}
