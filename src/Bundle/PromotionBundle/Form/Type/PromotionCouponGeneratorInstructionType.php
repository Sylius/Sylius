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

use Sylius\Component\Promotion\Factory\PromotionCouponGeneratorInstructionFactoryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\DataMapperInterface;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class PromotionCouponGeneratorInstructionType extends AbstractType implements DataMapperInterface
{
    /** @param array<string> $validationGroups */
    public function __construct(
        private DataMapperInterface $propertyPathDataMapper,
        private PromotionCouponGeneratorInstructionFactoryInterface $promotionCouponGeneratorInstructionFactory,
        private string $dataClass,
        private array $validationGroups = [],
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('amount', IntegerType::class, [
                'label' => 'sylius.form.promotion_coupon_generator_instruction.amount',
            ])
            ->add('prefix', TextType::class, [
                'label' => 'sylius.form.promotion_coupon_generator_instruction.prefix',
                'required' => false,
            ])
            ->add('codeLength', IntegerType::class, [
                'label' => 'sylius.form.promotion_coupon_generator_instruction.code_length',
            ])
            ->add('suffix', TextType::class, [
                'label' => 'sylius.form.promotion_coupon_generator_instruction.suffix',
                'required' => false,
            ])
            ->add('usageLimit', IntegerType::class, [
                'required' => false,
                'label' => 'sylius.form.promotion_coupon_generator_instruction.usage_limit',
            ])
            ->add('expiresAt', DateType::class, [
                'required' => false,
                'label' => 'sylius.form.promotion_coupon_generator_instruction.expires_at',
                'widget' => 'single_text',
            ])
            ->setDataMapper($this)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => $this->dataClass,
            'validation_groups' => $this->validationGroups,
        ]);
    }

    public function mapDataToForms($viewData, \Traversable $forms): void
    {
        $this->propertyPathDataMapper->mapDataToForms($viewData, $forms);
    }

    public function mapFormsToData(\Traversable $forms, &$viewData): void
    {
        $data = [];
        foreach ($forms as $form) {
            $data[$form->getName()] = $form->getData();
        }

        $viewData = $this->promotionCouponGeneratorInstructionFactory->createFromArray($data);
    }

    public function getBlockPrefix(): string
    {
        return 'sylius_promotion_coupon_generator_instruction';
    }
}
