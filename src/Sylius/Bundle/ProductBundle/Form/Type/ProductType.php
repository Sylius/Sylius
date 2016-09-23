<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ProductBundle\Form\Type;

use Sylius\Bundle\ProductBundle\Form\EventSubscriber\ProductOptionFieldSubscriber;
use Sylius\Bundle\ProductBundle\Form\EventSubscriber\SimpleProductSubscriber;
use Sylius\Bundle\ResourceBundle\Form\EventSubscriber\AddCodeFormSubscriber;
use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Sylius\Component\Product\Resolver\ProductVariantResolverInterface;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
class ProductType extends AbstractResourceType
{
    /**
     * @var ProductVariantResolverInterface
     */
    private $variantResolver;

    /**
     * @param string $dataClass FQCN
     * @param string[] $validationGroups
     * @param ProductVariantResolverInterface $variantResolver
     */
    public function __construct($dataClass, $validationGroups, ProductVariantResolverInterface $variantResolver)
    {
        parent::__construct($dataClass, $validationGroups);

        $this->variantResolver = $variantResolver;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->addEventSubscriber(new AddCodeFormSubscriber())
            ->addEventSubscriber(new ProductOptionFieldSubscriber($this->variantResolver))
            ->addEventSubscriber(new SimpleProductSubscriber())
            ->add('enabled', 'checkbox', [
                'required' => false,
                'label' => 'sylius.form.product.enabled',
            ])
            ->add('translations', 'sylius_translations', [
                'type' => 'sylius_product_translation',
                'label' => 'sylius.form.product.translations',
            ])
            ->add('attributes', 'collection', [
                'required' => false,
                'type' => 'sylius_product_attribute_value',
                'prototype' => false,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'label' => false,
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_product';
    }
}
