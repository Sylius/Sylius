<?php

namespace spec\Sylius\Component\Seo\Model\Twitter;

use PhpSpec\ObjectBehavior;

/**
 * @mixin \Sylius\Component\Seo\Model\Twitter\SummaryCard
 *
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class SummaryCardSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Seo\Model\Twitter\SummaryCard');
    }

    function it_implements_Summary_Card_interface()
    {
        $this->shouldImplement('Sylius\Component\Seo\Model\Twitter\SummaryCardInterface');
    }

    function it_has_correct_type()
    {
        $this->getType()->shouldReturn('summary');
    }

    function it_has_site()
    {
        $this->getSite()->shouldReturn(null);

        $this->setSite('@pamilme');
        $this->getSite()->shouldReturn('@pamilme');
    }

    function it_has_site_id()
    {
        $this->getSiteId()->shouldReturn(null);

        $this->setSiteId('753686598');
        $this->getSiteId()->shouldReturn('753686598');
    }

    function it_has_creator_id()
    {
        $this->getCreatorId()->shouldReturn(null);

        $this->setCreatorId('753686598');
        $this->getCreatorId()->shouldReturn('753686598');
    }

    function it_has_title()
    {
        $this->getTitle()->shouldReturn(null);

        $this->setTitle('Lorem ipsum');
        $this->getTitle()->shouldReturn('Lorem ipsum');
    }

    function it_has_description()
    {
        $this->getDescription()->shouldReturn(null);

        $this->setDescription('Lorem ipsum dolor sit amet');
        $this->getDescription()->shouldReturn('Lorem ipsum dolor sit amet');
    }

    function it_has_image()
    {
        $this->getImage()->shouldReturn(null);

        $this->setImage('http://sylius.org/assets/img/logo.png');
        $this->getImage()->shouldReturn('http://sylius.org/assets/img/logo.png');
    }
}
