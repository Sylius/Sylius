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

use Sylius\Bundle\RbacBundle\Form\EventSubscriber\AddParentFormSubscriber;
use Sylius\Bundle\ResourceBundle\Form\EventSubscriber\AddCodeFormSubscriber;
use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\FormBuilderInterface;

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
            ->add('name', 'text', [
                'label' => 'sylius.form.role.name',
            ])
            ->add('description', 'textarea', [
                'required' => false,
                'label' => 'sylius.form.role.description',
            ])
            ->add('securityRoles', 'sylius_security_role_choice', [
                'required' => false,
                'label' => 'sylius.form.role.security_roles',
            ])
            ->add('permissions', 'sylius_permission_choice', [
                'required' => false,
                'multiple' => true,
                'expanded' => true,
                'label' => 'sylius.form.role.permissions',
            ])
            ->addEventSubscriber(new AddCodeFormSubscriber())
            ->addEventSubscriber(new AddParentFormSubscriber('role'))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_role';
    }
}
