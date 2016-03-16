<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\ThemeBundle\Repository;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\ThemeBundle\Repository\FailsafeThemeRepository;
use Sylius\Bundle\ThemeBundle\Repository\ThemeRepositoryInterface;

/**
 * @mixin FailsafeThemeRepository
 *
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class FailsafeThemeRepositorySpec extends ObjectBehavior
{
    function let(ThemeRepositoryInterface $unstableRepository, ThemeRepositoryInterface $fallbackRepository)
    {
        $this->beConstructedWith($unstableRepository, $fallbackRepository);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ThemeBundle\Repository\FailsafeThemeRepository');
    }

    function it_implements_theme_repository_interface()
    {
        $this->shouldImplement(ThemeRepositoryInterface::class);
    }

    function it_proxies_any_method_to_usntable_repository(
        ThemeRepositoryInterface $unstableRepository,
        ThemeRepositoryInterface $fallbackRepository
    ) {
        $unstableRepository->find(42)->willReturn('result');
        $fallbackRepository->find(42)->shouldNotBeCalled();

        $this->find(42)->shouldReturn('result');
    }

    function it_proxies_any_method_to_fallback_repository_if_the_unstable_one_throws_an_exception(
        ThemeRepositoryInterface $unstableRepository,
        ThemeRepositoryInterface $fallbackRepository
    ) {
        $unstableRepository->find(42)->willThrow(\Exception::class);
        $fallbackRepository->find(42)->willReturn('result');

        $this->find(42)->shouldReturn('result');
    }
}
