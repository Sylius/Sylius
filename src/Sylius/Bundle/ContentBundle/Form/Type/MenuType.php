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
 * Menu block type.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class MenuType extends AbstractResourceType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options = [])
    {
        $builder
            ->add('parent', null, [
                'required' => true,
                'label' => 'sylius.form.menu.parent',
            ])
            ->add('label', 'text', [
                'required' => true,
                'label' => 'sylius.form.menu.label',
            ])
            ->add('name', 'text', [
                'required' => true,
                'label' => 'sylius.form.menu.name',
            ])
            ->add('children', 'collection', [
                'type' => 'sylius_menu_node',
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'label' => false,
                'button_add_label' => 'sylius.form.menu.add_menu_node',
                'cascade_validation' => true,
             ])
            ->add('uri', null, [
                'required' => false,
                'label' => 'sylius.form.menu.uri',
            ])
            ->add('route', null, [
                'required' => false,
                'label' => 'sylius.form.menu.route',
            ])
            ->add('display', null, [
                'required' => false,
                'label' => 'sylius.form.menu.display',
            ])
            ->add('displayChildren', null, [
                'required' => false,
                'label' => 'sylius.form.menu.display_children',
            ])
            ->add('publishable', null, [
                'label' => 'sylius.form.menu.publishable',
                ])
            ->add('publishStartDate', 'datetime', [
                'label' => 'sylius.form.menu.publish_start_date',
                'empty_value' => /* @Ignore */ ['year' => '-', 'month' => '-', 'day' => '-'],
                'time_widget' => 'text',
            ])
            ->add('publishEndDate', 'datetime', [
                'label' => 'sylius.form.menu.publish_end_date',
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
        return 'sylius_menu';
    }
}
