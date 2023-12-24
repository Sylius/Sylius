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
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

final class PromotionType extends AbstractResourceType
{
    public function __construct(
        string $dataClass,
        array $validationGroups,
        private string $promotionTranslationType,
    ) {
        parent::__construct($dataClass, $validationGroups);
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'sylius.form.promotion.name',
            ])
            ->add('translations', ResourceTranslationsType::class, [
                'entry_type' => $this->promotionTranslationType,
                'label' => 'sylius.form.promotion.translations',
            ])
            ->add('description', TextareaType::class, [
                'label' => 'sylius.form.promotion.description',
                'required' => false,
            ])
            ->add('exclusive', CheckboxType::class, [
                'label' => 'sylius.form.promotion.exclusive',
            ])
            ->add('appliesToDiscounted', CheckboxType::class, [
                'label' => 'sylius.form.promotion.applies_to_discounted',
            ])
            ->add('usageLimit', IntegerType::class, [
                'label' => 'sylius.form.promotion.usage_limit',
                'required' => false,
            ])
            ->add('startsAt', DateTimeType::class, [
                'label' => 'sylius.form.promotion.starts_at',
                'date_widget' => 'single_text',
                'time_widget' => 'single_text',
                'required' => false,
            ])
            ->add('endsAt', DateTimeType::class, [
                'label' => 'sylius.form.promotion.ends_at',
                'date_widget' => 'single_text',
                'time_widget' => 'single_text',
                'required' => false,
            ])
            ->add('priority', IntegerType::class, [
                'label' => 'sylius.form.promotion.priority',
                'required' => false,
            ])
            ->add('couponBased', CheckboxType::class, [
                'label' => 'sylius.form.promotion.coupon_based',
                'required' => false,
            ])
            ->add('rules', PromotionRuleCollectionType::class, [
                'label' => 'sylius.form.promotion.rules',
                'button_add_label' => 'sylius.form.promotion.add_rule',
            ])
            ->add('actions', PromotionActionCollectionType::class, [
                'label' => 'sylius.form.promotion.actions',
                'button_add_label' => 'sylius.form.promotion.add_action',
            ])
            ->addEventSubscriber(new AddCodeFormSubscriber())
        ;
    }

    public function getBlockPrefix(): string
    {
        return 'sylius_promotion';
    }
}
