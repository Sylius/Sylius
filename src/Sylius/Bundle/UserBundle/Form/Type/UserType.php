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
use Sylius\Component\Resource\Metadata\MetadataInterface;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
abstract class UserType extends AbstractResourceType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
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
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'data_class' => $this->dataClass,
                'validation_groups' => function (FormInterface $form) {
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
