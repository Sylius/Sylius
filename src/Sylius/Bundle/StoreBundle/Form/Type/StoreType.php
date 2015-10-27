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
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Sylius store form type.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class StoreType extends AbstractResourceType
{

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Sylius\Component\Store\Model\Store',
            'validation_groups' => array(),
        ));
    }
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
            ->add('enabled', null, array(
                'label' => 'sylius.form.store.enabled'
            ))
            ->add('address', 'text', array(
                'label' => 'sylius.form.store.address'
            ))
            ->add('geoloc', 'text', array(
                'label' => 'sylius.form.store.geoloc'
            ))
            ->add('user', null, array(
                'label' => 'sylius.form.store.user'
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
