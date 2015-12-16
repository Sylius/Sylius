<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ContentBundle\Form\Type;

use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Menu block type.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class MenuType extends AbstractResourceType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options = array())
    {
        $builder
            ->add('parent', null, array(
                'required' => true,
                'label' => 'sylius.form.menu.parent'
            ))
            ->add('label', 'text', array(
                'required' => true,
                'label' => 'sylius.form.menu.label'
            ))
            ->add('name', 'text', array(
                'required' => true,
                'label' => 'sylius.form.menu.name'
            ))
            ->add('children', 'collection', array(
                'type'             => 'sylius_menu_node',
                'allow_add'        => true,
                'allow_delete'     => true,
                'by_reference'     => false,
                'label'            => false,
                'button_add_label' => 'sylius.form.menu.add_menu_node',
                'cascade_validation' => true,
             ))
            ->add('uri', null, array(
                'required' => false,
                'label' => 'sylius.form.menu.uri'
            ))
            ->add('route', null, array(
                'required' => false,
                'label' => 'sylius.form.menu.route'
            ))
            ->add('display', null, array(
                'required' => false,
                'label' => 'sylius.form.menu.display'
            ))
            ->add('displayChildren', null, array(
                'required' => false,
                'label' => 'sylius.form.menu.display_children'
            ))
            ->add('publishable', null, array(
                'label' => 'sylius.form.menu.publishable'
                ))
            ->add('publishStartDate', 'datetime', array(
                'label' => 'sylius.form.menu.publish_start_date',
                'empty_value' =>/** @Ignore */ array('year' => '-', 'month' => '-', 'day' => '-'),
                'time_widget' => 'text',
            ))
            ->add('publishEndDate', 'datetime', array(
                'label' => 'sylius.form.menu.publish_end_date',
                'empty_value' =>/** @Ignore */ array('year' => '-', 'month' => '-', 'day' => '-'),
                'time_widget' => 'text',
            ))
        ;

    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'cascade_validation' => true,
        ));

        parent::setDefaultOptions($resolver);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_menu';
    }
}
