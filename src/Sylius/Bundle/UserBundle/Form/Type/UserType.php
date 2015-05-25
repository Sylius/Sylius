<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\UserBundle\Form\Type;

use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class UserType extends AbstractResourceType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('plainPassword', 'password', array(
                'label' => 'sylius.form.user.password.label',
            ))
            ->add('enabled', 'checkbox', array(
                'label' => 'sylius.form.user.enabled',
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $groups = $this->validationGroups;
        $resolver->setDefaults(array(
            'data_class' => $this->dataClass,
            'validation_groups' => function (FormInterface $form) use ($groups) {
                $data = $form->getData();
                if ($data && !$data->getId()) {
                    $groups[] = 'user_create';
                }

                return $groups;
            },
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_user';
    }
}
