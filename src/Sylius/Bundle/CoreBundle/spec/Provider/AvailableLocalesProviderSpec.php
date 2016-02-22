<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\CoreBundle\Provider;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Resource\Provider\AvailableLocalesProviderInterface;
use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @author Anna Walasek <anna.walasek@lakion.org>
 */
class AvailableLocalesProviderSpec extends ObjectBehavior
{
    function let(RepositoryInterface $localeRepository)
    {
        $this->beConstructedWith($localeRepository);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\Provider\AvailableLocalesProvider');
    }

    function it_implements_interface_available_translation_locales_provider()
    {
        $this->shouldImplement(AvailableLocalesProviderInterface::class);
    }

    function it_provides_available_locales(
        $localeRepository,
        LocaleInterface $firstLocale,
        LocaleInterface $secondLocale
    ) {

        $locales = [$firstLocale, $secondLocale];
        $localeRepository->findBy(['enabled' => true])->willReturn($locales);
        $firstLocale->getCode()->willReturn('en_US');
        $secondLocale->getCode()->willReturn('pl_PL');

        $this->getAvailableLocales()->shouldReturn(['en_US', 'pl_PL']);
    }
}
