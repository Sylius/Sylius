<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Metadata\Model\Twitter;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Metadata\Model\MetadataInterface;
use Sylius\Component\Metadata\Model\Twitter\PlayerCard;

/**
 * @mixin \Sylius\Component\Metadata\Model\Twitter\PlayerCard
 *
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class PlayerCardSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Metadata\Model\Twitter\PlayerCard');
    }

    function it_implements_Summary_Card_interface()
    {
        $this->shouldImplement('Sylius\Component\Metadata\Model\Twitter\PlayerCardInterface');
    }

    function it_is_serializable()
    {
        $that = new PlayerCard();
        $that->setTitle('Lorem ipsum');

        $that = unserialize(serialize($that));

        \PHPUnit_Framework_Assert::assertEquals('Lorem ipsum', $that->getTitle());
    }

    function it_is_mergeable_with_same_class_object()
    {
        $metadata = new PlayerCard();
        $metadata->setTitle('Lorem ipsum');
        $metadata->setDescription('Lorem ipsum dolor sit amet');

        $this->setTitle('Death Star');
        $this->setSite('@pamilme');

        $this->merge($metadata);

        $this->getTitle()->shouldReturn('Death Star');
        $this->getDescription()->shouldReturn('Lorem ipsum dolor sit amet');
        $this->getSite()->shouldReturn('@pamilme');
    }

    function it_can_not_merge_with_different_class_object(MetadataInterface $metadata)
    {
        $this->shouldThrow('\InvalidArgumentException')->duringMerge($metadata);
    }

    function it_has_correct_type()
    {
        $this->getType()->shouldReturn('player');
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

    function it_has_player()
    {
        $this->getPlayer()->shouldReturn(null);

        $this->setPlayer('https://example.com/player');
        $this->getPlayer()->shouldReturn('https://example.com/player');
    }

    function it_has_player_width()
    {
        $this->getPlayerWidth()->shouldReturn(null);

        $this->setPlayerWidth(1920);
        $this->getPlayerWidth()->shouldReturn(1920);
    }

    function it_has_player_height()
    {
        $this->getPlayerHeight()->shouldReturn(null);

        $this->setPlayerHeight(1080);
        $this->getPlayerHeight()->shouldReturn(1080);
    }

    function it_has_player_stream()
    {
        $this->getPlayerStream()->shouldReturn(null);

        $this->setPlayerStream('https://example.com/stream');
        $this->getPlayerStream()->shouldReturn('https://example.com/stream');
    }

    function it_has_player_stream_content_type()
    {
        $this->getPlayerStreamContentType()->shouldReturn(null);

        $this->setPlayerStreamContentType('mp4');
        $this->getPlayerStreamContentType()->shouldReturn('mp4');
    }
}
