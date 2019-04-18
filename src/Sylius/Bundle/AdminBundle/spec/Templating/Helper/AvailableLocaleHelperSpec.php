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

namespace spec\Sylius\Bundle\AdminBundle\Templating\Helper;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Resource\Translation\Provider\TranslationLocaleProviderInterface;
use Symfony\Component\Templating\Helper\Helper;

final class AvailableLocaleHelperSpec extends ObjectBehavior
{
    function let(TranslationLocaleProviderInterface $localeProvider): void
    {
        $this->beConstructedWith($localeProvider);
    }

    function it_is_templating_helper(): void
    {
        $this->shouldHaveType(Helper::class);
    }

    function it_provides_all_locales_defined_in_shop(
        TranslationLocaleProviderInterface $localeProvider
    ): void {
        $localeProvider->getDefinedLocalesCodes()->willReturn(['en_US', 'pl_PL']);

        $this->getDefindedLocalesCodes()->shouldReturn(['en_US', 'pl_PL']);
    }
}
