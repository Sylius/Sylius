<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Form\Type;

use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\FormBuilderInterface;

class ImageType extends AbstractResourceType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('file', 'file', array(
<<<<<<< HEAD
<<<<<<< HEAD
                    'label' => 'sylius.form.image.file',
                ))
                ->add('title', 'text', array(
                    'label' => 'sylius.form.image.title',
                    'required' => false,
                ))
                ->add('description', 'textarea', array(
                    'label' => 'sylius.form.image.description',
                    'required' => false,
        ));
        $builder->add('title', 'text', array(
            'label' => 'sylius.form.image.title',
            'required' => false
        ));
        $builder->add('description', 'textarea', array(
            'label' => 'sylius.form.image.description',
            'required' => false
=======
                    'label' => 'sylius.form.image.file'
=======
                    'label' => 'sylius.form.image.file',
>>>>>>> Updating indent
                ))
                ->add('title', 'text', array(
                    'label' => 'sylius.form.image.title',
                    'required' => false,
                ))
                ->add('description', 'textarea', array(
                    'label' => 'sylius.form.image.description',
<<<<<<< HEAD
                    'required' => false
>>>>>>> Using fluent setter
=======
                    'required' => false,
>>>>>>> Updating indent
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_image';
    }
}
