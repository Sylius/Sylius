<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\StoreBundle\Form\Type;

use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Sylius store form type.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class StoreType extends AbstractResourceType
{

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('code', 'text', array(
                'required' => false,
                'label'    => 'sylius.form.store.code'
            ))
            ->add('name', 'text', array(
                'label' => 'sylius.form.store.name'
            ))
            ->add('description', 'textarea', array(
                'required' => false,
                'label'    => 'sylius.form.store.description'
            ))
            ->add('address', 'text', array(
                'label' => 'sylius.form.store.address'
            ))
            ->add('geoloc', 'text', array(
                'label' => 'sylius.form.store.geoloc'
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_store';
    }
}
