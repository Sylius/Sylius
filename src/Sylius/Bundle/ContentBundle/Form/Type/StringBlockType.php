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
 * String block type.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class StringBlockType extends AbstractResourceType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('parentDocument', null, array(
                'label' => 'sylius.form.simple_block.parent'
            ))
            ->add('id', 'text', array(
                'label' => 'sylius.form.string_block.id'
            ))
            ->add('name', 'text', array(
                'label' => 'sylius.form.string_block.name'
            ))
            ->add('body', 'textarea', array(
                'required' => false,
                'label'    => 'sylius.form.string_block.body',
            ))
        ;

    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_string_block';
    }
}
