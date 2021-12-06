<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\ProductBundle\Form\Type;

use Sylius\Component\Product\Model\ProductInterface;
use Sylius\Component\Product\Model\ProductVariantInterface;
use Symfony\Bridge\Doctrine\Form\DataTransformer\CollectionToArrayTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ProductVariantChoiceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if ($options['multiple']) {
            $builder->addViewTransformer(new CollectionToArrayTransformer(), true);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'choices' => function (Options $options): iterable {
                    return $options['product']->getVariants();
                },
                'choice_value' => 'code',
                'choice_label' => function (ProductVariantInterface $variant): string {
                    /** @psalm-suppress InvalidCast */
                    return (string) $variant;
                },
                'choice_translation_domain' => false,
                'multiple' => false,
                'expanded' => true,
            ])
            ->setRequired([
                'product',
            ])
            ->setAllowedTypes('product', ProductInterface::class)
        ;
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }

    public function getBlockPrefix(): string
    {
        return 'sylius_product_variant_choice';
    }
}
