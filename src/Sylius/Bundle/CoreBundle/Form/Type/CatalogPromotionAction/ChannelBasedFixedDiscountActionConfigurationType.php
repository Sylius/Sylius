<?php

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Form\Type\CatalogPromotionAction;

use Sylius\Bundle\CoreBundle\Form\Type\ChannelCollectionType;
use Sylius\Bundle\PromotionBundle\Form\Type\CatalogPromotionAction\FixedDiscountActionConfigurationType;
use Sylius\Component\Core\Model\ChannelInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ChannelBasedFixedDiscountActionConfigurationType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'entry_type' => FixedDiscountActionConfigurationType::class,
            'entry_options' => function (ChannelInterface $channel) {
                return [
                    'label' => $channel->getName(),
                    'currency' => $channel->getBaseCurrency()->getCode(),
                ];
            },
        ]);
    }

    public function getParent(): string
    {
        return ChannelCollectionType::class;
    }
}
