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

namespace Sylius\Bundle\PromotionBundle\Form\Type;

use Sylius\Bundle\ResourceBundle\Form\EventSubscriber\AddCodeFormSubscriber;
use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Sylius\Bundle\ResourceBundle\Form\Type\ResourceTranslationsType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

final class CatalogPromotionType extends AbstractResourceType
{
    public function __construct(
        string $dataClass,
        array $validationGroups,
        private string $catalogPromotionTranslationType,
    ) {
        parent::__construct($dataClass, $validationGroups);
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->addEventSubscriber(new AddCodeFormSubscriber())
            ->add('name', TextType::class, [
                'label' => 'sylius.form.catalog_promotion.name',
            ])
            ->add('translations', ResourceTranslationsType::class, [
                'entry_type' => $this->catalogPromotionTranslationType,
                'label' => 'sylius.form.catalog_promotion.translations',
            ])
            ->add('enabled', CheckboxType::class, [
                'label' => 'sylius.form.catalog_promotion.enabled',
                'required' => false,
            ])
            ->add('priority', NumberType::class, [
                'label' => 'sylius.form.catalog_promotion.priority',
                'required' => false,
            ])
            ->add('startDate', DateTimeType::class, [
                'label' => 'sylius.form.catalog_promotion.start_date',
                'date_widget' => 'single_text',
                'time_widget' => 'single_text',
                'with_seconds' => true,
                'required' => false,
            ])
            ->add('endDate', DateTimeType::class, [
                'label' => 'sylius.form.catalog_promotion.end_date',
                'date_widget' => 'single_text',
                'time_widget' => 'single_text',
                'with_seconds' => true,
                'required' => false,
            ])
            ->add('exclusive', CheckboxType::class, [
              'label' => 'sylius.form.promotion.exclusive',
              'required' => false,
            ])
            ->add('scopes', CollectionType::class, [
                'label' => 'sylius.ui.scopes',
                'entry_type' => CatalogPromotionScopeType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'required' => false,
            ])
            ->add('actions', CollectionType::class, [
                'label' => 'sylius.ui.actions',
                'entry_type' => CatalogPromotionActionType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'required' => false,
            ])
        ;

        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event): void {
            $data = $event->getData();

            if (isset($data['startDate']) && $data['startDate']['date'] !== '' && $data['startDate']['time'] === '') {
                $data['startDate']['time'] = '00:00:00';
            }
            if (isset($data['endDate']) && $data['endDate']['date'] !== '' && $data['endDate']['time'] === '') {
                $data['endDate']['time'] = '23:59:59';
            }

            $event->setData($data);
        });
    }

    public function getBlockPrefix(): string
    {
        return 'sylius_catalog_promotion';
    }
}
