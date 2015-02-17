<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\RbacBundle\Form\Type;

use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

/**
 * RBAC Role form type.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class RoleType extends AbstractResourceType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text', array(
                'label' => 'sylius.form.role.name',
            ))
            ->add('description', 'textarea', array(
                'required' => false,
                'label'    => 'sylius.form.role.description',
            ))
            ->add('securityRoles', 'sylius_security_role_choice', array(
                'required' => false,
                'label'    => 'sylius.form.role.security_roles',
            ))
            ->add('parent', 'sylius_role_choice', array(
                'label'    => 'sylius.form.role.parent',
            ))
            ->add('permissions', 'sylius_permission_choice', array(
                'required' => false,
                'multiple' => true,
                'expanded' => true,
                'label'    => 'sylius.form.role.permissions'
            ))
        ;

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $role = $event->getData();
            $form = $event->getForm();

            if (null === $role || null === $role->getId()) {
                $form->add('code', 'text', array(
                    'label' => 'sylius.form.role.code'
                ));
            } else {
                $form->add('code', 'text', array(
                    'label'    => 'sylius.form.role.code',
                    'disabled' => true,

                ));
            }

            if (null !== $role && null !== $role->getId() && null === $role->getParent()) {
                $form->remove('parent');
            }
        });
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_role';
    }
}
