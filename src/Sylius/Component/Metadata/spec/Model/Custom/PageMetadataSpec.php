<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Metadata\Model\Custom;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Metadata\Model\Twitter\CardInterface;

/**
 * @mixin \Sylius\Component\Metadata\Model\Custom\PageMetadata
 *
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class PageMetadataSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Metadata\Model\Custom\PageMetadata');
    }

    function it_implements_Page_Metadata_interface()
    {
        $this->shouldImplement('Sylius\Component\Metadata\Model\Custom\PageMetadataInterface');
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

    function it_has_keywords()
    {
        $this->getKeywords()->shouldReturn([]);

        $this->setKeywords(['lorem', 'ipsum']);
        $this->getKeywords()->shouldReturn(['lorem', 'ipsum']);
    }

    function it_has_charset()
    {
        $this->getCharset()->shouldReturn('UTF-8');

        $this->setCharset('ISO-8859-2');
        $this->getCharset()->shouldReturn('ISO-8859-2');
    }

    function it_has_Twitter(CardInterface $card)
    {
        $this->getTwitter()->shouldReturn(null);

        $this->setTwitter($card);
        $this->getTwitter()->shouldReturn($card);

        $this->setTwitter(null);
        $this->getTitle()->shouldReturn(null);
    }
}
