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

namespace Sylius\Bundle\UserBundle\Form\Type;

use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Sylius\Bundle\UserBundle\Form\UserVerifiedAtToBooleanTransformer;
use Sylius\Component\Core\Model\ShopUser;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

abstract class UserType extends AbstractResourceType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username', TextType::class, [
                'label' => 'sylius.form.user.username',
            ])
            ->add('email', EmailType::class, [
                'label' => 'sylius.form.user.email',
            ])
            ->add('plainPassword', PasswordType::class, [
                'label' => 'sylius.form.user.password.label',
            ])
            ->add('enabled', CheckboxType::class, [
                'label' => 'sylius.form.user.enabled',
                'required' => false,
            ])
            ->add('verifiedAt', CheckboxType::class, [
                'label' => 'sylius.form.user.verified',
                'required' => false,
            ])
        ;

        $builder->get('verifiedAt')->addModelTransformer(new UserVerifiedAtToBooleanTransformer(), true);

        $builder->addEventListener(FormEvents::POST_SET_DATA, static function (FormEvent $event) {
            /** @var ShopUser|null $data */
            $data = $event->getData();
            if (null === $data) {
                return;
            }

            if ($data->isVerified()) {
                $event->getForm()->add('verifiedAt', CheckboxType::class, [
                    'label' => 'sylius.form.user.verified',
                    'required' => false,
                    'disabled' => true,
                    'data' => true,
                ]);
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefaults([
                'data_class' => $this->dataClass,
                'validation_groups' => function (FormInterface $form): array {
                    $data = $form->getData();
                    if ($data && !$data->getId()) {
                        $this->validationGroups[] = 'sylius_user_create';
                    }

                    return $this->validationGroups;
                },
            ])
        ;
    }
}
