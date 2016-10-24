<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\ThemeBundle\Translation\Provider;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ThemeBundle\Translation\Provider\Loader\TranslatorLoaderProvider;
use Sylius\Bundle\ThemeBundle\Translation\Provider\Loader\TranslatorLoaderProviderInterface;
use Symfony\Component\Translation\Loader\LoaderInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class TranslatorLoaderProviderSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(TranslatorLoaderProvider::class);
    }

    function it_implements_translation_loader_provider_interface()
    {
        $this->shouldImplement(TranslatorLoaderProviderInterface::class);
    }

    function it_returns_previously_received_loaders(
        LoaderInterface $firstLoader,
        LoaderInterface $secondLoader
    ) {
        $this->beConstructedWith(['first' => $firstLoader, 'second' => $secondLoader]);

        $this->getLoaders()->shouldReturn(['first' => $firstLoader, 'second' => $secondLoader]);
    }
}
