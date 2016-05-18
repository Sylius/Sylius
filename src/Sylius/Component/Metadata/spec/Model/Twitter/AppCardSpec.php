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
use Sylius\Component\Metadata\Model\Twitter\AppCard;

/**
 * @mixin \Sylius\Component\Metadata\Model\Twitter\AppCard
 *
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class AppCardSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Metadata\Model\Twitter\AppCard');
    }

    function it_implements_App_Card_interface()
    {
        $this->shouldImplement('Sylius\Component\Metadata\Model\Twitter\AppCardInterface');
    }

    function it_is_serializable()
    {
        $that = new AppCard();
        $that->setDescription('Lorem ipsum');

        $that = unserialize(serialize($that));

        \PHPUnit_Framework_Assert::assertEquals('Lorem ipsum', $that->getDescription());
    }

    function it_is_mergeable_with_same_class_object()
    {
        $metadata = new AppCard();
        $metadata->setSiteId('42');
        $metadata->setDescription('Lorem ipsum dolor sit amet');

        $this->setSiteId('753686598');
        $this->setSite('@pamilme');

        $this->merge($metadata);

        $this->getSiteId()->shouldReturn('753686598');
        $this->getDescription()->shouldReturn('Lorem ipsum dolor sit amet');
        $this->getSite()->shouldReturn('@pamilme');
    }

    function it_can_not_merge_with_different_class_object(MetadataInterface $metadata)
    {
        $this->shouldThrow('\InvalidArgumentException')->duringMerge($metadata);
    }

    function it_has_correct_type()
    {
        $this->getType()->shouldReturn('app');
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

    function it_has_description()
    {
        $this->getDescription()->shouldReturn(null);

        $this->setDescription('Lorem ipsum dolor sit amet');
        $this->getDescription()->shouldReturn('Lorem ipsum dolor sit amet');
    }

    function it_has_app_name_iphone()
    {
        $this->getAppNameIphone()->shouldReturn(null);

        $this->setAppNameIphone('Iphone App');
        $this->getAppNameIphone()->shouldReturn('Iphone App');
    }

    function it_has_app_id_iphone()
    {
        $this->getAppIdIphone()->shouldReturn(null);

        $this->setAppIdIphone('Iphone App ID');
        $this->getAppIdIphone()->shouldReturn('Iphone App ID');
    }

    function it_has_app_url_iphone()
    {
        $this->getAppUrlIphone()->shouldReturn(null);

        $this->setAppUrlIphone('https://example.com/iphone_app');
        $this->getAppUrlIphone()->shouldReturn('https://example.com/iphone_app');
    }

    function it_has_app_name_ipad()
    {
        $this->getAppNameIpad()->shouldReturn(null);

        $this->setAppNameIpad('Ipad App');
        $this->getAppNameIpad()->shouldReturn('Ipad App');
    }

    function it_has_app_id_ipad()
    {
        $this->getAppIdIpad()->shouldReturn(null);

        $this->setAppIdIpad('Ipad App ID');
        $this->getAppIdIpad()->shouldReturn('Ipad App ID');
    }

    function it_has_app_url_ipad()
    {
        $this->getAppUrlIpad()->shouldReturn(null);

        $this->setAppUrlIpad('https://example.com/ipad_app');
        $this->getAppUrlIpad()->shouldReturn('https://example.com/ipad_app');
    }

    function it_has_app_name_googleplay()
    {
        $this->getAppNameGoogleplay()->shouldReturn(null);

        $this->setAppNameGoogleplay('Googleplay App');
        $this->getAppNameGoogleplay()->shouldReturn('Googleplay App');
    }

    function it_has_app_id_googleplay()
    {
        $this->getAppIdGoogleplay()->shouldReturn(null);

        $this->setAppIdGoogleplay('Googleplay App ID');
        $this->getAppIdGoogleplay()->shouldReturn('Googleplay App ID');
    }

    function it_has_app_url_googleplay()
    {
        $this->getAppUrlGoogleplay()->shouldReturn(null);

        $this->setAppUrlGoogleplay('https://example.com/googleplay_app');
        $this->getAppUrlGoogleplay()->shouldReturn('https://example.com/googleplay_app');
    }
}
