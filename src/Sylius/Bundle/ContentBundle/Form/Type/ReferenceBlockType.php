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
 * Reference block type.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class ReferenceBlockType extends AbstractResourceType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options = [])
    {
        $builder
            ->add('id', 'text', [
                'label' => 'sylius.form.reference_block.id',
            ])
            ->add('title', 'text', [
                'label' => 'sylius.form.reference_block.title',
            ])
            ->add('body', 'textarea', [
                'required' => false,
                'label' => 'sylius.form.reference_block.body',
            ])
            ->add('publishable', null, [
                'label' => 'sylius.form.reference_block.publishable',
                ])
            ->add('publishStartDate', 'datetime', [
                'label' => 'sylius.form.reference_block.publish_start_date',
                'empty_value' => /* @Ignore */ ['year' => '-', 'month' => '-', 'day' => '-'],
                'time_widget' => 'text',
            ])
            ->add('publishEndDate', 'datetime', [
                'label' => 'sylius.form.reference_block.publish_end_date',
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
        return 'sylius_reference_block';
    }
}
