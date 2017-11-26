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

namespace spec\Sylius\Component\Resource\Translation\Provider;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Resource\Translation\Provider\TranslationLocaleProviderInterface;

final class ImmutableTranslationLocaleProviderSpec extends ObjectBehavior
{
    public function let(): void
    {
        $this->beConstructedWith(['pl_PL', 'en_US'], 'pl_PL');
    }

    public function it_implements_translation_locale_provider_interface(): void
    {
        $this->shouldImplement(TranslationLocaleProviderInterface::class);
    }

    public function it_returns_defined_locales_codes(): void
    {
        $this->getDefinedLocalesCodes()->shouldReturn(['pl_PL', 'en_US']);
    }

    public function it_returns_the_default_locale_code(): void
    {
        $this->getDefaultLocaleCode()->shouldReturn('pl_PL');
    }
}
