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
 * MenuNode block type.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class MenuNodeType extends AbstractResourceType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options = array())
    {
        $builder
            ->add('name', 'text', array(
                'required' => true,
                'label' => 'sylius.form.menu_node_block.internal_name'
            ))
            ->add('label', 'text', array(
                'required' => true,
                'label' => 'sylius.form.menu_node_block.label'
            ))
            ->add('display', null, array(
                'label' => 'sylius.form.menu_node_block.display'
            ))
            ->add('displayChildren', null, array(
                'label' => 'sylius.form.menu_node_block.display_children'
            ))
            ->add('linkType', 'choice', array(
                'required' => false,
                'choices' => array (
                        'auto' => 'auto',
                        'uri' => 'sylius.form.menu_node_block.uri',
                        'route' => 'route',
                        'content' => 'sylius.form.menu_node_block.content'
                ),
                'label'    => 'sylius.form.menu_node_block.link_type',
            ))
            ->add('publishable', null, array(
                'label' => 'sylius.form.menu_node_block.publishable'
                ))
            ->add('publishStartDate', 'datetime', array(
                'label' => 'sylius.form.menu_node_block.publish_start_date',
                'empty_value' =>/** @Ignore */ array('year' => '-', 'month' => '-', 'day' => '-'),
                'time_widget' => 'text',
            ))
            ->add('publishEndDate', 'datetime', array(
                'label' => 'sylius.form.menu_node_block.publish_end_date',
                'empty_value' =>/** @Ignore */ array('year' => '-', 'month' => '-', 'day' => '-'),
                'time_widget' => 'text',
            ))
            ->add('route', null, array(
                'label' => 'sylius.form.menu_node_block.route'
            ))
            ->add('content', 'sylius_content_choice', array(
                'label' => 'sylius.form.menu_node_block.content',
             ))
            ->add('uri', null, array(
                'label' => 'sylius.form.menu_node_block.uri'
            ))
        ;

    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_menu_node';
    }
}
