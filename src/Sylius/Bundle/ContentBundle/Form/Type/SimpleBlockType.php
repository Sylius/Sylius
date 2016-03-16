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
 * Simple block type.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class SimpleBlockType extends AbstractResourceType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options = [])
    {
        $builder
            ->add('parentDocument', null, [
                'label' => 'sylius.form.simple_block.parent',
            ])
            ->add('name', 'text', [
                'label' => 'sylius.form.simple_block.internal_name',
            ])
            ->add('title', 'text', [
                'label' => 'sylius.form.simple_block.title',
            ])
            ->add('body', 'textarea', [
                'required' => false,
                'label' => 'sylius.form.simple_block.body',
            ])
            ->add('publishable', null, [
                'label' => 'sylius.form.simple_block.publishable',
            ])
            ->add('publishStartDate', 'datetime', [
                'label' => 'sylius.form.simple_block.publish_start_date',
                'empty_value' => /* @Ignore */ ['year' => '-', 'month' => '-', 'day' => '-'],
                'time_widget' => 'text',
            ])
            ->add('publishEndDate', 'datetime', [
                'label' => 'sylius.form.simple_block.publish_end_date',
                'empty_value' => /* @Ignore */ ['year' => '-', 'month' => '-', 'day' => '-'],
                'time_widget' => 'text',
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_simple_block';
    }
}
