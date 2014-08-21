<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\TaxationBundle\Form\Type;

use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Tax rate form type.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class TaxRateType extends AbstractResourceType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('category', 'sylius_tax_category_choice', array(
                'label' => 'sylius.form.tax_rate.category'
            ))
            ->add('name', 'text', array(
                'label' => 'sylius.form.tax_rate.name'
            ))
            ->add('amount', 'percent', array(
                'label' => 'sylius.form.tax_rate.amount',
                'precision' => 3
            ))
            ->add('includedInPrice', 'checkbox', array(
                'label' => 'sylius.form.tax_rate.included_in_price'
            ))
            ->add('calculator', 'sylius_tax_calculator_choice', array(
                'label' => 'sylius.form.tax_rate.calculator'
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_tax_rate';
    }
}
