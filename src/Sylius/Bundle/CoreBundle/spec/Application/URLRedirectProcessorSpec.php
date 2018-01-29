<?php
/**
 * Created by PhpStorm.
 * User: mamazu
 * Date: 29/01/18
 * Time: 11:09
 */

declare(strict_types=1);

namespace spec\Sylius\Bundle\CoreBundle\Application;

use Prophecy\Argument;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\CoreBundle\Application\URLRedirectProcessorInterface;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Core\Model\URLRedirect;

class URLRedirectProcessorSpec extends ObjectBehavior
{

    public function it_implements_url_redirect_processor_interface()
    {
        $this->shouldImplement(URLRedirectProcessorInterface::class);
    }

    public function let(EntityRepository $repository)
    {
        $this->beConstructedWith($repository);
    }

    public function it_keeps_url_that_dont_have_a_redirect(EntityRepository $repository)
    {
        $repository->findOneBy(Argument::type('array'))->willReturn(null);

        $this->redirectRoute('route')->shouldBeEqualTo('route');
    }

    public function it_redirects_urls(EntityRepository $repository)
    {
        $repository->findOneBy(Argument::type('array'))->willReturn(new URLRedirect('abc', 'new_abc'));

        $this->redirectRoute('abc')->shouldBeEqualTo('new_abc');
    }
}
