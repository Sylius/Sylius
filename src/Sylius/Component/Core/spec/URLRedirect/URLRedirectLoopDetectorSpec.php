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
use Sylius\Component\Core\Model\URLRedirectInterface;
use Sylius\Component\Core\Repository\URLRedirectRepositoryInterface;
use Sylius\Component\Core\URLRedirect\URLRedirectLoopDetectorInterface;

final class URLRedirectLoopDetectorSpec extends ObjectBehavior
{
    public function it_implements_url_redirect_processor_interface()
    {
        $this->shouldImplement(URLRedirectLoopDetectorInterface::class);
    }

    public function let(URLRedirectRepositoryInterface $repository)
    {
        $this->beConstructedWith($repository);
    }

    public function it_finds_no_loop_with_empty_repository(
        URLRedirectRepositoryInterface $repository,
        URLRedirectInterface $newNode
    ) {
        $repository->getActiveRedirectForRoute('/efg')->shouldBeCalled()->willReturn(null);

        $newNode->getOldRoute()->willReturn('/abc');
        $newNode->getNewRoute()->willReturn('/efg');

        $this->containsLoop($newNode)->shouldBe(false);
    }

    public function it_finds_a_linear_route(URLRedirectRepositoryInterface $repository, URLRedirectInterface $newNode)
    {
        $repository->getActiveRedirectForRoute('/c')->shouldBeCalled()->willReturn(new URLRedirect('/c', '/d'));
        $repository->getActiveRedirectForRoute('/d')->shouldBeCalled()->willReturn(null);
        $repository->getActiveRedirectForRoute('/a')->shouldNotBeCalled();

        $newNode->getOldRoute()->willReturn('/b');
        $newNode->getNewRoute()->willReturn('/c');

        $this->containsLoop($newNode)->shouldBe(false);
    }

    public function it_finds_a_whole_loop(URLRedirectRepositoryInterface $repository, URLRedirectInterface $newNode)
    {
        $repository->getActiveRedirectForRoute('/a')->shouldBeCalled()->willReturn(new URLRedirect('/a', '/b'));
        $repository->getActiveRedirectForRoute('/b')->shouldBeCalled()->willReturn(new URLRedirect('/b', '/d'));
        $repository->getActiveRedirectForRoute('/d')->shouldNotBeCalled();

        $newNode->getOldRoute()->willReturn('/d');
        $newNode->getNewRoute()->willReturn('/a');

        $this->containsLoop($newNode)->shouldBe(true);
    }

    public function it_finds_a_partial_loop(URLRedirectRepositoryInterface $repository, URLRedirectInterface $newNode)
    {
        $repository->getActiveRedirectForRoute('/a')->shouldBeCalled()->willReturn(new URLRedirect('/a', '/b'));
        $repository->getActiveRedirectForRoute('/b')->shouldNotBeCalled();

        $newNode->getOldRoute()->willReturn('/b');
        $newNode->getNewRoute()->willReturn('/a');

        $this->containsLoop($newNode)->shouldBe(true);
    }
}
