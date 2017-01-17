<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Form\Extension;

use Sylius\Bundle\CoreBundle\Form\Type\User\ShopUserType;
use Sylius\Bundle\CustomerBundle\Form\Type\CustomerType;
use Sylius\Bundle\CoreBundle\Form\EventSubscriber\AddUserFormSubscriber;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @author Michał Marcinkowski <michal.marcinkowski@lakion.com>
 */
final class CustomerTypeExtension extends AbstractTypeExtension
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addEventSubscriber(new AddUserFormSubscriber(ShopUserType::class));
    }

    /**
     * {@inheritdoc}
     */
    public function getExtendedType()
    {
        return CustomerType::class;
    }
}
