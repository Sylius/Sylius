<?php
/**
 * Created by PhpStorm.
 * User: mamazu
 * Date: 29/01/18
 * Time: 11:09
 */

declare(strict_types=1);

namespace spec\Sylius\Component\Core\URLRedirect;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\URLRedirect;
use Sylius\Component\Core\Repository\URLRedirectRepositoryInterface;
use Sylius\Component\Core\URLRedirect\URLRedirectProcessorInterface;

class URLRedirectProcessorSpec extends ObjectBehavior
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
