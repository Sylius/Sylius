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

use Sylius\Bundle\PromotionBundle\Form\Type\PromotionRuleType;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Promotion\ChannelAwareConfigurationInterface;
use Sylius\Component\Promotion\Model\PromotionRuleInterface;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

final class PromotionRuleTypeExtension extends AbstractTypeExtension
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

        $builder->add(ChannelAwareConfigurationInterface::EXCLUDED_CHANNELS_CONFIGURATION_KEY, ChoiceType::class, [
            'label' => 'sylius.form.promotion_rule.channels',
            'choices' => $choices,
            'multiple' => true,
            'expanded' => true,
            'required' => false,
            'choice_translation_domain' => false,
            'mapped' => false,
        ]);

        $builder->addEventListener(FormEvents::POST_SET_DATA, function (FormEvent $event) use ($availableCodes): void {
            $data = $event->getData();
            if (!$data instanceof PromotionRuleInterface) {
                $event->getForm()->get(ChannelAwareConfigurationInterface::EXCLUDED_CHANNELS_CONFIGURATION_KEY)
                    ->setData($availableCodes);

                return;
            }
            $excludedCodes = $data->getConfiguration()[ChannelAwareConfigurationInterface::EXCLUDED_CHANNELS_CONFIGURATION_KEY] ?? [];
            $checkedCodes = $excludedCodes === [] ? $availableCodes : array_values(array_diff($availableCodes, $excludedCodes));
            $event->getForm()->get(ChannelAwareConfigurationInterface::EXCLUDED_CHANNELS_CONFIGURATION_KEY)
                ->setData($checkedCodes);
        });

        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) use ($availableCodes): void {
            $data = $event->getData();
            $entityData = $event->getForm()->getData();
            if (is_array($data)
                && !array_key_exists(ChannelAwareConfigurationInterface::EXCLUDED_CHANNELS_CONFIGURATION_KEY, $data)
                && !$entityData instanceof PromotionRuleInterface) {
                $data[ChannelAwareConfigurationInterface::EXCLUDED_CHANNELS_CONFIGURATION_KEY] = $availableCodes;
                $event->setData($data);
            }
        });

        $builder->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) use ($availableCodes): void {
            $data = $event->getData();
            if (!$data instanceof PromotionRuleInterface) {
                return;
            }
            $configuration = $data->getConfiguration();
            $selectedChannels = $event->getForm()->get(ChannelAwareConfigurationInterface::EXCLUDED_CHANNELS_CONFIGURATION_KEY)->getData() ?? [];
            $configuration[ChannelAwareConfigurationInterface::EXCLUDED_CHANNELS_CONFIGURATION_KEY] =
                array_values(array_diff($availableCodes, $selectedChannels));
            $data->setConfiguration($configuration);
        });
    }

    public static function getExtendedTypes(): iterable
    {
        yield PromotionRuleType::class;
    }
}
