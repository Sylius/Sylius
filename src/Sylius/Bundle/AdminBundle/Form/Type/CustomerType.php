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

use Sylius\Bundle\CoreBundle\Form\Type\User\ShopUserType;
use Sylius\Bundle\CustomerBundle\Form\Type\CustomerType as BaseCustomerType;
use Sylius\Component\User\Model\UserAwareInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Validator\Constraints\Valid;
use Webmozart\Assert\Assert;

final class CustomerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('user', ShopUserType::class, ['constraints' => [new Valid()], 'required' => false]);

        $builder->addEventListener(FormEvents::SUBMIT, function (FormEvent $event): void {
            $data = $event->getData();
            $form = $event->getForm();

            /** @var UserAwareInterface $data */
            Assert::isInstanceOf($data, UserAwareInterface::class);

            if (null === $data->getUser()->getPlainPassword() && null === $data->getUser()->getId()) {
                $data->setUser(null);
                $event->setData($data);

                $form->remove('user');
                $form->add('user', ShopUserType::class, ['constraints' => [new Valid()]]);
            }
        });
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
