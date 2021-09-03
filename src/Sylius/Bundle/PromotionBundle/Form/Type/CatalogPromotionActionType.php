<?php

declare(strict_types=1);

namespace Sylius\Bundle\PromotionBundle\Form\Type;

use Sylius\Bundle\PromotionBundle\Form\Type\CatalogAction\PercentageDiscountActionConfigurationType;
use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;

final class CatalogPromotionActionType extends AbstractResourceType
{
    private array $actionTypes;

    public function __construct(string $dataClass, array $validationGroups = [], array $actionTypes = [])
    {
        parent::__construct($dataClass, $validationGroups);

        $this->actionTypes = $actionTypes;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('type', ChoiceType::class, [
                'label' => 'sylius.ui.type',
                'choices' => $this->actionTypes,
            ])
            ->add('configuration', PercentageDiscountActionConfigurationType::class, [
                'label' => false,
            ])
        ;
    }

    public function getBlockPrefix(): string
    {
        return 'sylius_catalog_promotion_action';
    }

}
