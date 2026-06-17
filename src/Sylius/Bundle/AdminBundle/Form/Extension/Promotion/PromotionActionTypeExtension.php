<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\AdminBundle\Form\Extension\Promotion;

use Sylius\Bundle\PromotionBundle\Form\Type\PromotionActionType;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Promotion\ChannelAwareConfigurationInterface;
use Sylius\Component\Promotion\Model\PromotionActionInterface;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

final class PromotionActionTypeExtension extends AbstractTypeExtension
{
    public function __construct(private ChannelRepositoryInterface $channelRepository)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('type', HiddenType::class);

        $choices = [];
        foreach ($this->channelRepository->findAll() as $channel) {
            $choices[$channel->getName()] = $channel->getCode();
        }
        $availableCodes = array_values($choices);

        $builder->add(ChannelAwareConfigurationInterface::CHANNELS_CONFIGURATION_KEY, ChoiceType::class, [
            'label' => 'sylius.form.promotion_action.channels',
            'choices' => $choices,
            'multiple' => true,
            'expanded' => true,
            'required' => false,
            'choice_translation_domain' => false,
            'mapped' => false,
        ]);

        $builder->addEventListener(FormEvents::POST_SET_DATA, function (FormEvent $event) use ($availableCodes): void {
            $data = $event->getData();
            if (!$data instanceof PromotionActionInterface) {
                return;
            }
            $storedCodes = $data->getConfiguration()[ChannelAwareConfigurationInterface::CHANNELS_CONFIGURATION_KEY] ?? [];
            if ($storedCodes === []) {
                $storedCodes = $availableCodes;
            }
            $event->getForm()->get(ChannelAwareConfigurationInterface::CHANNELS_CONFIGURATION_KEY)
                ->setData(array_values(array_intersect($storedCodes, $availableCodes)));
        });

        $builder->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) use ($availableCodes): void {
            $data = $event->getData();
            if (!$data instanceof PromotionActionInterface) {
                return;
            }
            $configuration = $data->getConfiguration();
            $selectedChannels = $event->getForm()->get(ChannelAwareConfigurationInterface::CHANNELS_CONFIGURATION_KEY)->getData() ?? [];
            $sortedSelected = $selectedChannels;
            $sortedAvailable = $availableCodes;
            sort($sortedSelected);
            sort($sortedAvailable);
            $configuration[ChannelAwareConfigurationInterface::CHANNELS_CONFIGURATION_KEY] =
                ($sortedSelected === $sortedAvailable) ? [] : $selectedChannels;
            $data->setConfiguration($configuration);
        });
    }

    public static function getExtendedTypes(): iterable
    {
        yield PromotionActionType::class;
    }
}
