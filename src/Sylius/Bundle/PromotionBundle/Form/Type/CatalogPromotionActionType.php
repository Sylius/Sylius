<?php

declare(strict_types=1);

namespace Sylius\Bundle\PromotionBundle\Form\Type;

use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Sylius\Component\Promotion\Model\CatalogPromotionActionInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

final class CatalogPromotionActionType extends AbstractResourceType
{
    private array $actionTypes = [];

    private array $actionConfigurationTypes;

    public function __construct(
        string $dataClass,
        array $validationGroups,
        iterable $actionConfigurationTypes
    ) {
        parent::__construct($dataClass, $validationGroups);

        foreach ($actionConfigurationTypes as $type => $formType) {
            $this->actionConfigurationTypes[$type] = get_class($formType);
            $this->actionTypes['sylius.form.catalog_promotion.action.' . $type] = $type;
        }
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $defaultActionType = current($this->actionTypes);
        $defaultActionConfigurationType = $this->actionConfigurationTypes[$defaultActionType];

        $builder
            ->add('type', ChoiceType::class, [
                'label' => 'sylius.ui.type',
                'choices' => $this->actionTypes,
            ])
            ->add('configuration', $defaultActionConfigurationType, [
                'label' => false,
            ])
        ;

        $builder
            ->addEventListener(FormEvents::PRE_SET_DATA, function(FormEvent $event): void {
                /** @var CatalogPromotionActionInterface|null $data */
                $data = $event->getData();
                if ($data === null) {
                    return;
                }

                $form = $event->getForm();

                $actionConfigurationType = $this->actionConfigurationTypes[$data->getType()];
                $form->add('configuration', $actionConfigurationType, [
                    'label' => false,
                ]);
            })
            ->addEventListener(FormEvents::PRE_SUBMIT, function(FormEvent $event): void {
                /** @var array|null $data */
                $data = $event->getData();
                if ($data === null) {
                    return;
                }

                $form = $event->getForm();
                $formData = $form->getData();

                if ($formData !== null) {
                    $formData->setType($data['type']);
                    $formData->setConfiguration($data['configuration']);

                    if ($data['type'] === CatalogPromotionActionInterface::TYPE_FIXED_DISCOUNT) {
                        foreach ($data['configuration'] as $channelConfiguration) {
                            if ($channelConfiguration['amount'] === '') {
                                return;
                            }
                        }
                    }

                    $form->setData($formData);
                }

                $actionConfigurationType = $this->actionConfigurationTypes[$data['type']];
                $form->add('configuration', $actionConfigurationType, [
                    'label' => false,
                ]);
            })
            ->addEventListener(FormEvents::SUBMIT, function(FormEvent $event): void {
                /** @var CatalogPromotionActionInterface|null $data */
                $data = $event->getData();
                if ($data === null) {
                    return;
                }

                $form = $event->getForm();

                $actionConfigurationType = $this->actionConfigurationTypes[$data->getType()];
                $form->add('configuration', $actionConfigurationType, [
                    'label' => false,
                ]);
            })
        ;
    }

    public function getBlockPrefix(): string
    {
        return 'sylius_catalog_promotion_action';
    }
}
