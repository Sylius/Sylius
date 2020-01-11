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

use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

final class ProductTranslationType extends AbstractResourceType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'sylius.form.product.name',
            ])
            ->add('slug', TextType::class, [
                'label' => 'sylius.form.product.slug',
            ])
            ->add('description', TextareaType::class, [
                'required' => false,
                'label' => 'sylius.form.product.description',
            ])
            ->add('metaKeywords', TextType::class, [
                'required' => false,
                'label' => 'sylius.form.product.meta_keywords',
            ])
            ->add('metaDescription', TextType::class, [
                'required' => false,
                'label' => 'sylius.form.product.meta_description',
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(): string
    {
        return 'sylius_product_translation';
    }
}
