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
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class MenuNodeType extends AbstractResourceType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options = [])
    {
        $builder
            ->add('name', 'text', [
                'required' => true,
                'label' => 'sylius.form.menu_node_block.internal_name',
            ])
            ->add('label', 'text', [
                'required' => true,
                'label' => 'sylius.form.menu_node_block.label',
            ])
            ->add('display', null, [
                'label' => 'sylius.form.menu_node_block.display',
            ])
            ->add('displayChildren', null, [
                'label' => 'sylius.form.menu_node_block.display_children',
            ])
            ->add('linkType', 'choice', [
                'required' => false,
                'choices' => [
                        'auto' => 'auto',
                        'uri' => 'sylius.form.menu_node_block.uri',
                        'route' => 'route',
                        'content' => 'sylius.form.menu_node_block.content',
                ],
                'label' => 'sylius.form.menu_node_block.link_type',
            ])
            ->add('publishable', null, [
                'label' => 'sylius.form.menu_node_block.publishable',
                ])
            ->add('publishStartDate', 'datetime', [
                'label' => 'sylius.form.menu_node_block.publish_start_date',
                'empty_value' => /* @Ignore */ ['year' => '-', 'month' => '-', 'day' => '-'],
                'time_widget' => 'text',
            ])
            ->add('publishEndDate', 'datetime', [
                'label' => 'sylius.form.menu_node_block.publish_end_date',
                'empty_value' => /* @Ignore */ ['year' => '-', 'month' => '-', 'day' => '-'],
                'time_widget' => 'text',
            ])
            ->add('route', null, [
                'label' => 'sylius.form.menu_node_block.route',
            ])
            ->add('content', 'sylius_static_content_choice', [
                'label' => 'sylius.form.menu_node_block.content',
                'property' => 'title',
             ])
            ->add('uri', null, [
                'label' => 'sylius.form.menu_node_block.uri',
            ])
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
