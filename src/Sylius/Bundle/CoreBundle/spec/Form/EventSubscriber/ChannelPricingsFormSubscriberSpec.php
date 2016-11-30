<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\CoreBundle\Form\EventSubscriber;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\CoreBundle\Form\EventSubscriber\ChannelPricingsFormSubscriber;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ChannelPricingInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
final class ChannelPricingsFormSubscriberSpec extends ObjectBehavior
{
    function let(ChannelRepositoryInterface $channelRepository, FactoryInterface $channelPricingFactory)
    {
        $this->beConstructedWith($channelRepository, $channelPricingFactory);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ChannelPricingsFormSubscriber::class);
    }

    function it_implements_event_subscriber_interface()
    {
        $this->shouldImplement(EventSubscriberInterface::class);
    }

    function it_listens_on_pre_set_data_event()
    {
        $this->getSubscribedEvents()->shouldReturn([FormEvents::PRE_SET_DATA => 'preSetData']);
    }

    function it_adds_missing_channel_pricings_on_pre_set_data(
        ChannelInterface $firstChannel,
        ChannelInterface $secondChannel,
        ChannelPricingInterface $channelPricing,
        ChannelRepositoryInterface $channelRepository,
        FactoryInterface $channelPricingFactory,
        FormEvent $formEvent,
        ProductVariantInterface $productVariant
    ) {
        $formEvent->getData()->willReturn($productVariant);

        $channelRepository->findAll()->willReturn([$firstChannel, $secondChannel]);

        $productVariant->hasChannelPricingForChannel($firstChannel)->willReturn(true);
        $productVariant->hasChannelPricingForChannel($secondChannel)->willReturn(false);

        $channelPricingFactory->createNew()->willReturn($channelPricing);
        $channelPricing->setChannel($secondChannel)->shouldBeCalled();
        $productVariant->addChannelPricing($channelPricing)->shouldBeCalled();

        $this->preSetData($formEvent);
    }
}
