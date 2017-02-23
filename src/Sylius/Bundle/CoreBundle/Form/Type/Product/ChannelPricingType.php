<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Form\Type\Product;

use Sylius\Bundle\MoneyBundle\Form\Type\MoneyType;
use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
final class ChannelPricingType extends AbstractResourceType
{
    /**
     * @var ChannelRepositoryInterface
     */
    private $channelRepository;

    /**
     * {@inheritdoc}
     *
     * @param ChannelRepositoryInterface $channelRepository
     */
    public function __construct(
        $dataClass,
        $validationGroups = [],
        ChannelRepositoryInterface $channelRepository
    ) {
        parent::__construct($dataClass, $validationGroups);

        $this->channelRepository = $channelRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $channelRepository = $this->channelRepository;

        $builder
            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($channelRepository, $options) {
                /** @var ChannelInterface $channel */
                $channel = (isset($options['channel'])) ? $options['channel'] : $channelRepository->findOneByCode($event->getData()->getChannel());
                $form = $event->getForm();

                $form->add('price', MoneyType::class, [
                    'label' => $channel->getName(),
                    'currency' => $channel->getBaseCurrency()->getCode(),
                ]);
            })
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver
            ->setRequired('channel')
            ->setDefault('channel', null)
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'sylius_channel_pricing';
    }
}
