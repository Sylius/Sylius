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

namespace spec\Sylius\Bundle\ThemeBundle\Translation\Provider;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ThemeBundle\Translation\Provider\Loader\TranslatorLoaderProviderInterface;
use Symfony\Component\Translation\Loader\LoaderInterface;

final class TranslatorLoaderProviderSpec extends ObjectBehavior
{
    function it_implements_translation_loader_provider_interface(): void
    {
        $this->shouldImplement(TranslatorLoaderProviderInterface::class);
    }

    function it_returns_previously_received_loaders(
        LoaderInterface $firstLoader,
        LoaderInterface $secondLoader
    ): void {
        $this->beConstructedWith(['first' => $firstLoader, 'second' => $secondLoader]);

        $this->getLoaders()->shouldReturn(['first' => $firstLoader, 'second' => $secondLoader]);
    }
}
