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

/**
 * Customer filter form type.
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class CustomerFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('query', 'text', array(
                'label' => 'sylius.form.customer_filter.query',
                'attr'  => array(
                    'placeholder' => 'sylius.form.customer_filter.query'
                )
            ))
        ;
    }

    public function getName()
    {
        return 'sylius_customer_filter';
    }
}
