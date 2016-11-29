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
use Sylius\Component\Resource\Exception\UnexpectedTypeException;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Factory\TranslatableFactory;
use Sylius\Component\Resource\Factory\TranslatableFactoryInterface;
use Sylius\Component\Resource\Model\TranslatableInterface;
use Sylius\Component\Resource\Provider\TranslationLocaleProviderInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
final class TranslatableFactorySpec extends ObjectBehavior
{
    function let(FactoryInterface $factory, TranslationLocaleProviderInterface $localeProvider)
    {
        $this->beConstructedWith($factory, $localeProvider);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(TranslatableFactory::class);
    }

    function it_implements_translatable_factory_interface()
    {
        $this->shouldImplement(TranslatableFactoryInterface::class);
    }

    function it_throws_an_exception_if_resource_is_not_translatable(FactoryInterface $factory, \stdClass $resource)
    {
        $factory->createNew()->willReturn($resource);

        $this
            ->shouldThrow(UnexpectedTypeException::class)
            ->during('createNew')
        ;
    }

    function it_creates_translatable_and_sets_locales(
        FactoryInterface $factory,
        TranslationLocaleProviderInterface $localeProvider,
        TranslatableInterface $resource
    ) {
        $localeProvider->getDefaultLocaleCode()->willReturn('pl_PL');

        $factory->createNew()->willReturn($resource);

        $resource->setCurrentLocale('pl_PL')->shouldBeCalled();
        $resource->setFallbackLocale('pl_PL')->shouldBeCalled();

        $this->createNew()->shouldReturn($resource);
    }
}
