<?php

/*
* This file is part of the Sylius package.
*
* (c) Paweł Jędrzejewski
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Sylius\Bundle\CoreBundle\Form\Type\Filter;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Products filter form type.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class ProductFilterType extends AbstractType
{
    /**
     * FQCN taxon model class name.
     *
     * @var string
     */
    protected $taxonClass;

    /**
     * @param string $taxonClass FQCN taxon model class name
     */
    public function __construct($taxonClass)
    {
        $this->taxonClass = $taxonClass;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text', array(
                'required' => false,
                'label'    => 'sylius.form.product_filter.name',
                'attr'     => array(
                    'placeholder' => 'sylius.form.product_filter.name'
                )
            ))
            ->add('sku', 'text', array(
                'required' => false,
                'label'    => 'sylius.form.product_filter.sku',
                'attr'     => array(
                    'placeholder' => 'sylius.form.product_filter.sku'
                )
            ))
            ->add('channels', 'sylius_channel_choice', array(
                'required'    => false,
                'multiple'    => true,
                'empty_value' => 'sylius.form.product_filter.channels.empty_value',
            ))
            ->add('categories', 'entity', array(
                'required'    => false,
                'multiple'    => true,
                'class'       => $this->taxonClass,
                'empty_value' => 'sylius.form.product_filter.categories.empty_value',
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults(array(
                'data_class' => null
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_product_filter';
    }
}
