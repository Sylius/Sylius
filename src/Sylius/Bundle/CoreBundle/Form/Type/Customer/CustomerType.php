<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Form\Type\Customer;

use Sylius\Bundle\CoreBundle\Form\Type\User\ShopUserType;
use Sylius\Bundle\CustomerBundle\Form\Type\CustomerType as BaseCustomerType;
use Sylius\Bundle\UserBundle\Form\EventSubscriber\AddUserFormSubscriber;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @author Michał Marcinkowski <michal.marcinkowski@lakion.com>
 */
class CustomerType extends BaseCustomerType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder->addEventSubscriber(new AddUserFormSubscriber(ShopUserType::class));
    }
}
