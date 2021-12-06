<?php

declare(strict_types=1);

namespace Sylius\Bundle\PromotionBundle\Form\Type\CatalogPromotionAction;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PercentType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

final class PercentageDiscountActionConfigurationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('amount', PercentType::class, [
                'label' => 'sylius.ui.amount',
                'constraints' => [
                    new NotBlank(['groups' => ['sylius']]),
                ]
            ])
        ;
    }

    public function getBlockPrefix(): string
    {
        return 'sylius_catalog_promotion_action_percentage_discount_configuration';
    }
}
