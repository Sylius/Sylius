<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Translation\Factory;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Translation\Provider\LocaleProviderInterface;
use spec\Sylius\Component\Translation\Fixtures\SampleTranslatableResource;

require_once __DIR__.'/../Fixtures/SampleTranslatableResource.php';

/**
 * @mixin \Sylius\Component\Translation\Factory\TranslatableFactory
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class TranslatableFactorySpec extends ObjectBehavior
{
    function let(LocaleProviderInterface $localeProvider)
    {
        $this->beConstructedWith('spec\Sylius\Component\Translation\Fixtures\SampleTranslatableResource', $localeProvider);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Translation\Factory\TranslatableFactory');
    }
    
    function it_implements_translatable_factory_interface()
    {
        $this->shouldImplement('Sylius\Component\Translation\Factory\TranslatableFactoryInterface');
    }

    function it_creates_translatable_and_sets_locales(LocaleProviderInterface $localeProvider)
    {
        $localeProvider->getCurrentLocale()->willReturn('pl_PL');
        $localeProvider->getFallbackLocale()->willReturn('en_GB');

        $this->createNew()->shouldHaveType(SampleTranslatableResource::class);
    }
}
