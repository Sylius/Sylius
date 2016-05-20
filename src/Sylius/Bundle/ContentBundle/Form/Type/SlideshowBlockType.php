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
 * Slideshow block type.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class SlideshowBlockType extends AbstractResourceType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options = [])
    {
        $builder
            ->add('parentDocument', null, [
                'label' => 'sylius.form.slideshow_block.parent',
            ])
            ->add('name', 'text', [
                'label' => 'sylius.form.slideshow_block.internal_name',
            ])
            ->add('title', 'text', [
                'label' => 'sylius.form.slideshow_block.title',
            ])
            ->add('children', 'collection', [
                'type' => 'sylius_imagine_block',
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'label' => false,
                'button_add_label' => 'sylius.form.slideshow_block.add_slide',
                'cascade_validation' => true,
            ])
            ->add('publishable', null, [
                'label' => 'sylius.form.slideshow_block.publishable',
            ])
            ->add('publishStartDate', 'datetime', [
                'label' => 'sylius.form.slideshow_block.publish_start_date',
                'empty_value' => /* @Ignore */ ['year' => '-', 'month' => '-', 'day' => '-'],
                'time_widget' => 'text',
            ])
            ->add('publishEndDate', 'datetime', [
                'label' => 'sylius.form.slideshow_block.publish_end_date',
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
        return 'sylius_slideshow_block';
    }
}
