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
 * RBAC Persmission type form type.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class PermissionType extends AbstractResourceType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('description', 'textarea', array(
                'label' => 'sylius.form.permission.description',
            ))
            ->add('parent', 'sylius_permission_choice', array(
                'label'    => 'sylius.form.permission.parent',
            ))
        ;

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $permission = $event->getData();
            $form = $event->getForm();

            if (null === $permission || null === $permission->getId()) {
                $form->add('code', 'text', array(
                    'label' => 'sylius.form.permission.code',
                ));
            } else {
                $form->add('code', 'text', array(
                    'label'    => 'sylius.form.permission.code',
                    'disabled' => true,

                ));
            }

            if (null !== $permission && null !== $permission->getId() && null === $permission->getParent()) {
                $form->remove('parent');
            }
        });
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_permission';
    }
}
