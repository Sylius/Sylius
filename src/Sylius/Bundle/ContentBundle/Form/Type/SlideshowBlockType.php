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
 * Slideshow block type.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class SlideshowBlockType extends AbstractResourceType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('parentDocument', null, array(
                'label' => 'sylius.form.slideshow_block.parent'
            ))
            ->add('name', 'text', array(
                'label' => 'sylius.form.slideshow_block.internal_name'
            ))
            ->add('title', 'text', array(
                'label' => 'sylius.form.slideshow_block.title'
            ))
            ->add('children', 'collection', array(
                'type'         => 'sylius_imagine_block',
                'allow_add'    => true,
                'allow_delete' => true,
                'by_reference' => false,
                'label'        => 'sylius.form.slideshow_block.childrens'
             ))
        ;

    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_slideshow_block';
    }
}
