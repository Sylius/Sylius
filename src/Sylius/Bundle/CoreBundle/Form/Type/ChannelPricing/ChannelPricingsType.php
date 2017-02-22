<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Form\Type\ChannelPricing;

use Sylius\Bundle\CoreBundle\Form\Type\Product\ChannelPricingType;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ChannelPricing;
use Sylius\Component\Core\Model\ChannelPricingInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class ChannelPricingsType extends AbstractType implements EventSubscriberInterface
{
    /**
     * @var ChannelRepositoryInterface
     */
    private $channelRepository;

    /**
     * @param ChannelRepositoryInterface $channelRepository
     */
    public function __construct(ChannelRepositoryInterface $channelRepository)
    {
        $this->channelRepository = $channelRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->addEventSubscriber($this)
            ->addModelTransformer(new CallbackTransformer(
                function($value) {
                    $channelPricings = [];
                    /** @var ChannelPricingInterface $channelPricing */
                    foreach ($value as $channelPricing) {
                        $channelPricings[$channelPricing->getChannel()->getCode()] = $channelPricing;
                    }

                    return $channelPricings;
                },
                function($value) {
                    return $value;
                }
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            FormEvents::POST_SET_DATA => 'preSetData',
            FormEvents::SUBMIT => 'submit',
        ];
    }

    /**
     * @param FormEvent $event
     */
    public function preSetData(FormEvent $event)
    {
        $form = $event->getForm();

        /** @var FormInterface $child */
        foreach ($form as $child) {
            $form->remove($child->getName());
        }

        /** @var ProductVariantInterface $variant */
        $variant = $form->getParent()->getData();

        /** @var ChannelInterface $channel */
        foreach ($this->channelRepository->findAll() as $channel) {
            if ($variant->hasChannelPricingForChannel($channel)) {
                $form->add($channel->getCode(), ChannelPricingType::class, [
                    'channel' => $channel,
                    'data' => $variant->getChannelPricingForChannel($channel),
                ]);

                continue;
            }

            $form->add($channel->getCode(), ChannelPricingType::class, [
                'channel' => $channel,
            ]);
        }
    }

    /**
     * @param FormEvent $event
     */
    public function submit(FormEvent $event)
    {
        /** @var ChannelPricing[] $channelPricings */
        $channelPricings = $event->getData();
        $variant = $event->getForm()->getParent()->getData();

        foreach ($channelPricings as $channelCode => $channelPricing) {
            if (null === $channelPricing) {
                unset($channelPricings[$channelCode]);

                continue;
            }

            $channelPricing->setChannel($this->channelRepository->findOneByCode($channelCode));
            $channelPricing->setProductVariant($variant);
        }

        $event->setData($channelPricings);
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return CollectionType::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'sylius_channel_pricings';
    }
}
