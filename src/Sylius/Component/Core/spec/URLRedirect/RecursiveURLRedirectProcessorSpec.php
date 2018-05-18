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

namespace spec\Sylius\Component\Core\URLRedirect;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\URLRedirect;
use Sylius\Component\Core\Repository\URLRedirectRepositoryInterface;
use Sylius\Component\Core\URLRedirect\URLRedirectProcessorInterface;

final class RecursiveURLRedirectProcessorSpec extends ObjectBehavior
{
    public function it_implements_url_redirect_processor_interface()
    {
        $this->shouldImplement(URLRedirectProcessorInterface::class);
    }

    public function let(URLRedirectRepositoryInterface $repository)
    {
        $this->beConstructedWith($repository);
    }

    public function it_keeps_url_that_does_not_have_a_redirect(URLRedirectRepositoryInterface $repository)
    {
        $repository->getActiveRedirectForRoute('/route')->shouldBeCalled()->willReturn(null);

        $this->redirectRoute('/route')->shouldBeEqualTo('/route');
    }

    public function it_redirects_urls(URLRedirectRepositoryInterface $repository)
    {
        $activeRedirect = new URLRedirect('/route', '/new_route');
        $repository->getActiveRedirectForRoute('/route')->shouldBeCalled()->willReturn($activeRedirect);
        $repository->getActiveRedirectForRoute('/new_route')->shouldBeCalled()->willReturn(null);

        $this->redirectRoute('/route')->shouldBeEqualTo('/new_route');
    }
}
