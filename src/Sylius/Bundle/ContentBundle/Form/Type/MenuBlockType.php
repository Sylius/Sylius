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

/**
 * Menu block type.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class MenuBlockType extends AbstractResourceType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options = array())
    {
        $builder
            ->add('id', 'text', array(
                'label' => 'sylius.form.menu_block.id'
            ))
            ->add('name', 'text', array(
                'label' => 'sylius.form.menu_block.name'
            ))
            ->add('body', 'textarea', array(
                'required' => false,
                'label'    => 'sylius.form.menu_block.body',
            ))
            ->add('publishable', null, array(
                'label' => 'sylius.form.menu_block.publishable'
                ))
            ->add('publishStartDate', 'datetime', array(
                'label' => 'sylius.form.menu_block.publish_start_date',
                'empty_value' =>/** @Ignore */ array('year' => '-', 'month' => '-', 'day' => '-'),
                'time_widget' => 'text',
            ))
            ->add('publishEndDate', 'datetime', array(
                'label' => 'sylius.form.menu_block.publish_end_date',
                'empty_value' =>/** @Ignore */ array('year' => '-', 'month' => '-', 'day' => '-'),
                'time_widget' => 'text',
            ))
        ;

    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_menu_block';
    }
}
