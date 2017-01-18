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

use Sylius\Bundle\ProductBundle\Form\EventSubscriber\BuildAttributesFormSubscriber;
use Sylius\Bundle\ProductBundle\Form\EventSubscriber\ProductOptionFieldSubscriber;
use Sylius\Bundle\ProductBundle\Form\EventSubscriber\SimpleProductSubscriber;
use Sylius\Bundle\ResourceBundle\Form\EventSubscriber\AddCodeFormSubscriber;
use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Sylius\Bundle\ResourceBundle\Form\Type\ResourceTranslationsType;
use Sylius\Component\Product\Resolver\ProductVariantResolverInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Translation\Provider\TranslationLocaleProviderInterface;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
final class ProductType extends AbstractResourceType
{
    /**
     * @var ProductVariantResolverInterface
     */
    private $variantResolver;

    /**
     * @var FactoryInterface
     */
    private $attributeValueFactory;

    /**
     * @var TranslationLocaleProviderInterface
     */
    private $localeProvider;

    /**
     * @param string $dataClass
     * @param string[] $validationGroups
     * @param ProductVariantResolverInterface $variantResolver
     * @param FactoryInterface $attributeValueFactory
     * @param TranslationLocaleProviderInterface $localeProvider
     */
    public function __construct(
        $dataClass,
        $validationGroups,
        ProductVariantResolverInterface $variantResolver,
        FactoryInterface $attributeValueFactory,
        TranslationLocaleProviderInterface $localeProvider
    ) {
        parent::__construct($dataClass, $validationGroups);

        $this->variantResolver = $variantResolver;
        $this->attributeValueFactory = $attributeValueFactory;
        $this->localeProvider = $localeProvider;
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
            ->addEventSubscriber(new BuildAttributesFormSubscriber($this->attributeValueFactory, $this->localeProvider))
            ->add('enabled', CheckboxType::class, [
                'required' => false,
                'label' => 'sylius.form.product.enabled',
            ])
            ->add('translations', ResourceTranslationsType::class, [
                'entry_type' => ProductTranslationType::class,
                'label' => 'sylius.form.product.translations',
            ])
            ->add('attributes', CollectionType::class, [
                'entry_type' => ProductAttributeValueType::class,
                'required' => false,
                'prototype' => true,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'label' => false,
            ])
            ->add('associations', ProductAssociationsType::class, [
                'label' => false,
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'sylius_product';
    }
}
