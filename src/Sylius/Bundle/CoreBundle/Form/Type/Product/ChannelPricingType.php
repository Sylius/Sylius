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
use Sylius\Component\Core\Model\ChannelInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
final class ChannelPricingType extends AbstractResourceType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
                /** @var ChannelInterface $channel */
                $channel = $event->getData()->getChannel();
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
    public function getBlockPrefix()
    {
        return 'sylius_channel_pricing';
    }
}
