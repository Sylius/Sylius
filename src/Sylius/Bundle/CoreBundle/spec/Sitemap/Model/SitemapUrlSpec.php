<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
 
namespace spec\Sylius\Bundle\CoreBundle\Sitemap\Model;
 
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\CoreBundle\Sitemap\Model\ChangeFrequency;
use Sylius\Bundle\CoreBundle\Sitemap\Model\SitemapUrlInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
class SitemapUrlSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\Sitemap\Model\SitemapUrl');
    }

    function it_implements_sitemap_url_interface()
    {
        $this->shouldImplement(SitemapUrlInterface::class);
    }

    function it_has_localization()
    {
        $this->setLocalization('http://sylius.org/');
        $this->getLocalization()->shouldReturn('http://sylius.org/');
    }

    function it_has_last_modification(\DateTime $now)
    {
        $this->setLastModification($now);
        $this->getLastModification()->shouldReturn($now);
    }

    function it_has_change_frequency()
    {
        $this->setChangeFrequency(ChangeFrequency::always());
        $this->getChangeFrequency()->shouldReturn('always');
    }

    function it_has_priority()
    {
        $this->setPriority(0.5);
        $this->getPriority()->shouldReturn(0.5);
    }

    function it_throws_invalid_argument_exception_if_priority_wont_be_between_zero_and_one()
    {
        $this->shouldThrow(\InvalidArgumentException::class)->during('setPriority', array(-1));
        $this->shouldThrow(\InvalidArgumentException::class)->during('setPriority', array(-0.5));
        $this->shouldThrow(\InvalidArgumentException::class)->during('setPriority', array(2));
        $this->shouldThrow(\InvalidArgumentException::class)->during('setPriority', array(1.1));
    }

    function it_throws_invalid_argument_exception_if_priority_will_be_not_a_number()
    {
        $this->shouldThrow(\InvalidArgumentException::class)->during('setPriority', array('Mike'));
        $this->shouldThrow(\InvalidArgumentException::class)->during('setPriority', array(true));
    }
}
