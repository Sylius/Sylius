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
 * RBAC Permission type form type.
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
            ->add('description', 'textarea', [
                'label' => 'sylius.form.permission.description',
            ])
            ->addEventSubscriber(new AddCodeFormSubscriber())
            ->addEventSubscriber(new AddParentFormSubscriber('permission'))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_permission';
    }
}
