<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Resource\Factory;

use PhpSpec\ObjectBehavior;
use spec\Sylius\Component\Resource\Fixtures\SampleNonTranslatableResource;
use spec\Sylius\Component\Resource\Fixtures\SampleTranslatableResource;
use Sylius\Component\Resource\Exception\UnexpectedTypeException;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Factory\TranslatableFactoryInterface;
use Sylius\Component\Resource\Provider\LocaleProviderInterface;

require_once __DIR__.'/../Fixtures/SampleTranslatableResource.php';
require_once __DIR__.'/../Fixtures/SampleNonTranslatableResource.php';

/**
 * @mixin \Sylius\Component\Resource\Factory\TranslatableFactory
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
final class TranslatableFactorySpec extends ObjectBehavior
{
    function let(FactoryInterface $factory, LocaleProviderInterface $localeProvider)
    {
        $this->beConstructedWith($factory, $localeProvider);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Resource\Factory\TranslatableFactory');
    }

    function it_implements_translatable_factory_interface()
    {
        $this->shouldImplement(TranslatableFactoryInterface::class);
    }

    function it_throws_an_exception_if_resource_is_not_translatable(FactoryInterface $factory, SampleNonTranslatableResource $resource)
    {
        $factory->createNew()->willReturn($resource);

        $this
            ->shouldThrow(UnexpectedTypeException::class)
            ->during('createNew')
        ;
    }

    function it_creates_translatable_and_sets_locales(FactoryInterface $factory, LocaleProviderInterface $localeProvider, SampleTranslatableResource $resource)
    {
        $localeProvider->getCurrentLocale()->willReturn('pl_PL');
        $localeProvider->getFallbackLocale()->willReturn('en_GB');

        $factory->createNew()->willReturn($resource);

        $resource->setCurrentLocale('pl_PL')->shouldBeCalled();
        $resource->setFallbackLocale('en_GB')->shouldBeCalled();

        $this->createNew()->shouldReturn($resource);
    }
}
