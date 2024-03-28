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

namespace Sylius\Bundle\CoreBundle\Form\Type\Product;

use Doctrine\ORM\QueryBuilder;
use Sylius\Bundle\CoreBundle\Form\Type\ImageType;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Core\Model\ProductInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ProductImageType extends ImageType
{
    public function __construct(string $dataClass, private string $productVariantClass, array $validationGroups = [])
    {
        parent::__construct($dataClass, $validationGroups);
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options);

        if (isset($options['product']) && $options['product'] instanceof ProductInterface && $options['product']->getId() !== null) {
            $builder
                ->add('productVariants', EntityType::class, [
                    'class' => $this->productVariantClass,
                    'label' => 'sylius.ui.product_variants',
                    'multiple' => true,
                    'required' => false,
                    'choice_label' => 'descriptor',
                    'choice_value' => 'code',
                    'query_builder' => function (EntityRepository $er) use ($options): QueryBuilder {
                        return $er->createQueryBuilder('o')
                            ->where('o.product = :product')
                            ->setParameter('product', $options['product'])
                        ;
                    },
                    'autocomplete' => true,
                ])
            ;
        }
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        parent::buildView($view, $form, $options);

        $view->vars['product'] = $options['product'];
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);

        $resolver->setDefined('product');
        $resolver->setAllowedTypes('product', ProductInterface::class);
    }

    public function getBlockPrefix(): string
    {
        return 'sylius_product_image';
    }
}
