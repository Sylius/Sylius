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
        $this->shouldImplement('Sylius\Bundle\CoreBundle\Sitemap\Model\SitemapUrlInterface');
    }

    function it_is_template_aware()
    {
        $this->shouldImplement('Sylius\Bundle\CoreBundle\Sitemap\Renderer\TemplateAware');
    }

    function it_has_loc()
    {
        $this->setLoc('http://sylius.org/');
        $this->getLoc()->shouldReturn('http://sylius.org/');
    }

    function it_has_lastmod()
    {
        $now = new \DateTime();

        $this->setLastmod($now);
        $this->getLastmod()->shouldReturn($now);
    }

    function it_has_changefreq()
    {
        $this->setChangefreq('always');
        $this->getChangefreq()->shouldReturn('always');
    }

    function it_has_priority()
    {
        $this->setPriority(0.5);
        $this->getPriority()->shouldReturn(0.5);
    }

    function it_throws_invalid_argument_exception_if_priority_wont_be_between_zero_and_one()
    {
        $this->shouldThrow('\InvalidArgumentException')->during('setPriority', array(-1));
        $this->shouldThrow('\InvalidArgumentException')->during('setPriority', array(-0.5));
        $this->shouldThrow('\InvalidArgumentException')->during('setPriority', array(2));
        $this->shouldThrow('\InvalidArgumentException')->during('setPriority', array(1.1));
    }

    function it_throws_invalid_argument_exception_if_priority_will_be_not_a_number()
    {
        $this->shouldThrow('\InvalidArgumentException')->during('setPriority', array('Mike'));
        $this->shouldThrow('\InvalidArgumentException')->during('setPriority', array(true));
    }

    function it_throws_invalid_argument_exception_if_changefreq_will_be_not_supported()
    {
        $this->shouldThrow('\InvalidArgumentException')->during('setChangefreq', array('John'));
        $this->shouldThrow('\InvalidArgumentException')->during('setChangefreq', array(1));
        $this->shouldThrow('\InvalidArgumentException')->during('setChangefreq', array(true));
    }

    function it_has_template()
    {
        $this->setTemplate('SyliusCoreBundle:Sitemap:url_set.xml.twig');
    }
}
