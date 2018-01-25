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

namespace Sylius\Bundle\CoreBundle\Form\Type\Product;

use Sylius\Bundle\CoreBundle\Form\Type\ImageType;
use Sylius\Bundle\ResourceBundle\Form\Type\ResourceAutocompleteChoiceType;
use Sylius\Component\Core\Model\ProductInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ProductImageType extends ImageType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options);

        if (isset($options['product']) && $options['product'] instanceof ProductInterface) {
            $builder
                ->add('productVariants', ResourceAutocompleteChoiceType::class, [
                    'label' => 'sylius.ui.product_variants',
                    'multiple' => true,
                    'required' => false,
                    'choice_name' => 'descriptor',
                    'choice_value' => 'code',
                    'resource' => 'sylius.product_variant',
                ])
            ;
        }
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        parent::buildView($view, $form, $options);

        $view->vars['product'] = $options['product'];
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver->setDefined('product');
        $resolver->setAllowedTypes('product', ProductInterface::class);
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(): string
    {
        return 'sylius_product_image';
    }
}
