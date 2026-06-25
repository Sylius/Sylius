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

namespace Sylius\Bundle\AdminBundle\Form\Builder;

use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Promotion\ChannelAwareConfigurationInterface;
use Sylius\Component\Promotion\Model\ConfigurablePromotionElementInterface;
use Sylius\Component\Promotion\Model\PromotionActionInterface;
use Sylius\Component\Promotion\Model\PromotionRuleInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

final readonly class ChannelExclusionFormBuilder
{
    public function __construct(private ChannelRepositoryInterface $channelRepository)
    {
    }

    /**
     * @param class-string<ConfigurablePromotionElementInterface> $subjectClass
     */
    public function build(FormBuilderInterface $builder, string $label, string $subjectClass): void
    {
        $builder->add('type', HiddenType::class);

        $choices = [];
        foreach ($this->channelRepository->findAll() as $channel) {
            $choices[$channel->getName()] = $channel->getCode();
        }
        $availableCodes = array_values($choices);

        $builder->add(ChannelAwareConfigurationInterface::EXCLUDED_CHANNELS_CONFIGURATION_KEY, ChoiceType::class, [
            'label' => $label,
            'choices' => $choices,
            'multiple' => true,
            'expanded' => true,
            'required' => false,
            'choice_translation_domain' => false,
            'mapped' => false,
        ]);

        $builder->addEventListener(FormEvents::POST_SET_DATA, function (FormEvent $event) use ($availableCodes, $subjectClass): void {
            $data = $event->getData();
            if (!$data instanceof $subjectClass) {
                $event->getForm()->get(ChannelAwareConfigurationInterface::EXCLUDED_CHANNELS_CONFIGURATION_KEY)
                    ->setData($availableCodes);

                return;
            }
            $excludedCodes = $data->getConfiguration()[ChannelAwareConfigurationInterface::EXCLUDED_CHANNELS_CONFIGURATION_KEY] ?? [];
            $checkedCodes = $excludedCodes === [] ? $availableCodes : array_values(array_diff($availableCodes, $excludedCodes));
            $event->getForm()->get(ChannelAwareConfigurationInterface::EXCLUDED_CHANNELS_CONFIGURATION_KEY)
                ->setData($checkedCodes);
        });

        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) use ($availableCodes, $subjectClass): void {
            $data = $event->getData();
            $entityData = $event->getForm()->getData();
            if (is_array($data) &&
                !array_key_exists(ChannelAwareConfigurationInterface::EXCLUDED_CHANNELS_CONFIGURATION_KEY, $data) &&
                !$entityData instanceof $subjectClass) {
                $data[ChannelAwareConfigurationInterface::EXCLUDED_CHANNELS_CONFIGURATION_KEY] = $availableCodes;
                $event->setData($data);
            }
        });

        $builder->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) use ($availableCodes, $subjectClass): void {
            $data = $event->getData();
            if (!$data instanceof $subjectClass) {
                return;
            }

            \assert($data instanceof PromotionActionInterface || $data instanceof PromotionRuleInterface);

            $configuration = $data->getConfiguration();
            $selectedChannels = $event->getForm()->get(ChannelAwareConfigurationInterface::EXCLUDED_CHANNELS_CONFIGURATION_KEY)->getData() ?? [];
            $configuration[ChannelAwareConfigurationInterface::EXCLUDED_CHANNELS_CONFIGURATION_KEY] =
                array_values(array_diff($availableCodes, $selectedChannels));
            $data->setConfiguration($configuration);
        });
    }
}
