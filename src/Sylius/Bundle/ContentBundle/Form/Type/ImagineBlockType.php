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
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Menu block type.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class ImagineBlockType extends AbstractResourceType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text', array(
                'label' => 'sylius.form.imagine_block.internal_name'
            ))
            ->add('label', 'text', array(
                    'required' => false
            ))
            ->add('linkUrl', 'text', array(
                    'required' => false
            ))
            ->add('filter', 'choice', array(
                    'choices' => array(
                            'slideshow_small' => 'Slideshow Small (side)',
                            'slideshow_medium' => 'Slideshow Medium (content)',
                            'slideshow_large' => 'Slideshow Large (full width)',
                    ),
                    'required' => false
            ))
             ->add('image', 'cmf_media_image', array(
                     'required' => false
             ))
//             ->add('position', 'hidden', array(
//                     'mapped' => false
//             ))
//             ->add('title', 'text', array(
//                 'label' => 'sylius.form.imagine_block.title'
//             ))
//             ->add('children', 'collection', array(
//                        'type'         => 'sylius_imagine_block',
//                        'allow_add'    => true,
//                        'allow_delete' => true,
//                        'by_reference' => false,
//                        'label'        => 'sylius.form.imagine_block.childrens'
//              ))
            ;

    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_imagine_block';
    }
}
