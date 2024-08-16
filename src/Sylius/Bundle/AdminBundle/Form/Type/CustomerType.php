<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\AdminBundle\Form\Type;

use Sylius\Bundle\AdminBundle\Form\EventSubscriber\AddUserFormSubscriber;
use Sylius\Bundle\CoreBundle\Form\Type\User\ShopUserType;
use Sylius\Bundle\CustomerBundle\Form\Type\CustomerType as BaseCustomerType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Valid;

final class CustomerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstName', TextType::class, [
                'required' => false,
            ])
            ->add('lastName', TextType::class, [
                'required' => false,
            ])
            ->add('user', ShopUserType::class, [
                'constraints' => [new Valid()],
                'required' => false,
            ]);

        $builder->addEventSubscriber(new AddUserFormSubscriber());
    }

    public function getBlockPrefix(): string
    {
        return 'sylius_admin_customer';
    }

    public function getParent(): string
    {
        return BaseCustomerType::class;
    }
}
