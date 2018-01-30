<?php
/**
 * Created by PhpStorm.
 * User: mamazu
 * Date: 29/01/18
 * Time: 11:09
 */

declare(strict_types=1);

namespace spec\Sylius\Component\Core\URLRedirect;

use Doctrine\ORM\EntityRepository;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Core\Model\URLRedirect;
use Sylius\Component\Core\Model\URLRedirectInterface;
use Sylius\Component\Core\URLRedirect\URLRedirectLoopDetectorInterface;

class URLRedirectLoopDetectorSpec extends ObjectBehavior
{

    public function it_implements_url_redirect_processor_interface()
    {
        $this->shouldImplement(URLRedirectLoopDetectorInterface::class);
    }

    public function let(EntityRepository $repository)
    {
        $this->beConstructedWith($repository);
    }

    public function it_finds_no_loop_with_empty_repository(EntityRepository $repository, URLRedirectInterface $newNode)
    {
        $repository->findOneBy(Argument::type('array'))->willReturn();

        $newNode->getOldRoute()->willReturn('/abc');
        $newNode->getNewRoute()->willReturn('/efg');

        $this->containsLoop($newNode)->shouldBe(false);
    }

    public function it_finds_a_linear_route(EntityRepository $repository, URLRedirectInterface $newNode)
    {
        $repository->findOneBy(Argument::type('array'))->will(function ($array){
            $oldRoute = $array[0]['oldRoute'];
            switch($oldRoute){
                case '/a':
                    return new URLRedirect('/a', '/b');
                case '/c':
                    return new URLRedirect('/c', '/d');
                case '/d':
                    return null;
                default:
                    throw new \Exception('Invalid Method call with route '. $oldRoute);
            }
        });

        $newNode->getOldRoute()->willReturn('/b');
        $newNode->getNewRoute()->willReturn('/c');

        $this->containsLoop($newNode)->shouldBe(false);
    }

    public function it_finds_a_whole_loop(EntityRepository $repository, URLRedirectInterface $newNode)
    {
        $repository->findOneBy(Argument::type('array'))->will(function ($array){
            $oldRoute = $array[0]['oldRoute'];
            switch($oldRoute){
                case '/a':
                    return new URLRedirect('/a', '/b');
                case '/b':
                    return new URLRedirect('/b', '/d');
                case '/d':
                    return null;
                default:
                    throw new \Exception('Invalid Method call with route '. $oldRoute);
            }
        });

        $newNode->getOldRoute()->willReturn('/d');
        $newNode->getNewRoute()->willReturn('/a');

        $this->containsLoop($newNode)->shouldBe(true);
    }

    public function it_finds_a_partial_loop(EntityRepository $repository, URLRedirectInterface $newNode)
    {
        $repository->findOneBy(Argument::type('array'))->will(function ($array){
            $oldRoute = $array[0]['oldRoute'];
            switch($oldRoute){
                case '/a':
                    return new URLRedirect('/a', '/b');
                case '/b':
                    return new URLRedirect('/b', '/d');
                case '/d':
                    return null;
                default:
                    throw new \Exception('Invalid Method call with route '. $oldRoute);
            }
        });

        $newNode->getOldRoute()->willReturn('/b');
        $newNode->getNewRoute()->willReturn('/a');

        $this->containsLoop($newNode)->shouldBe(true);
    }

}
