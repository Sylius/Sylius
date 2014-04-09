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

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Product form type.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class ProductType extends AbstractType
{
    /**
     * Validation groups.
     *
     * @var array
     */
    protected $validationGroups;

    /**
     * Constructor.
     *
     * @param string $dataClass
     * @param array  $validationGroups
     */
    public function __construct($dataClass, array $validationGroups)
    {
        $this->dataClass = $dataClass;
        $this->validationGroups = $validationGroups;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text', array(
                'label' => 'sylius.form.product.name'
            ))
            ->add('description', 'textarea', array(
                'label' => 'sylius.form.product.description'
            ))
            ->add('masterVariant', 'sylius_product_variant', array(
                'master' => true,
            ))
            ->add('attributes', 'collection', array(
                'required'     => false,
                'type'         => 'sylius_product_attribute_value',
                'allow_add'    => true,
                'allow_delete' => true,
                'by_reference' => false
            ))
            ->add('options', 'sylius_product_option_choice', array(
                'required' => false,
                'multiple' => true,
                'label'    => 'sylius.form.product.options'
            ))
            ->add('metaKeywords', 'text', array(
                'required' => false,
                'label'    => 'sylius.form.product.meta_keywords'
            ))
            ->add('metaDescription', 'text', array(
                'required' => false,
                'label'    => 'sylius.form.product.meta_description'
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setDefaults(array(
                'data_class'        => $this->dataClass,
                'validation_groups' => $this->validationGroups
            ))
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
