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
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Simple static content type.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class StaticContentType extends AbstractResourceType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options = [])
    {
        $builder
            ->add('publishable', null, [
                'label' => 'sylius.form.static_content.publishable',
            ])
            ->add('id', 'text', [
                'label' => 'sylius.form.static_content.id',
            ])
            ->add('parent', null, [
                'label' => 'sylius.form.static_content.parent',
            ])
            ->add('name', 'text', [
                'label' => 'sylius.form.static_content.internal_name',
            ])
            ->add('locale', 'text', [
                'label' => 'sylius.form.static_content.title',
            ])
            ->add('title', 'text', [
                'label' => 'sylius.form.static_content.title',
            ])
            ->add('routes', 'collection', [
                'type' => 'sylius_route',
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'label' => 'sylius.form.static_content.routes',
                'cascade_validation' => true,
             ])
            ->add('menuNodes', 'collection', [
                'type' => 'sylius_menu_node',
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'label' => 'sylius.form.static_content.menu_nodes',
                'cascade_validation' => true,
             ])
            ->add('body', 'textarea', [
                'required' => false,
                'label' => 'sylius.form.static_content.body',
            ])
            ->add('publishStartDate', 'datetime', [
                'label' => 'sylius.form.static_content.publish_start_date',
                'empty_value' => /* @Ignore */ ['year' => '-', 'month' => '-', 'day' => '-'],
                'time_widget' => 'text',
            ])
            ->add('publishEndDate', 'datetime', [
                'label' => 'sylius.form.static_content.publish_end_date',
                'empty_value' => /* @Ignore */ ['year' => '-', 'month' => '-', 'day' => '-'],
                'time_widget' => 'text',
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'cascade_validation' => true,
        ]);

        parent::configureOptions($resolver);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_static_content';
    }
}
