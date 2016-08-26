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
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class UserType extends AbstractResourceType
{
    /**
     * @var MetadataInterface
     */
    private $metadata;

    /**
     * @param string $dataClass
     * @param array $validationGroups
     * @param MetadataInterface $metadata
     */
    public function __construct($dataClass, array $validationGroups = [], MetadataInterface $metadata)
    {
        parent::__construct($dataClass, $validationGroups);

        $this->metadata = $metadata;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', 'text', [
                'label' => 'sylius.form.user.username',
            ])
            ->add('email', 'email', [
                'label' => 'sylius.form.user.email',
            ])
            ->add('plainPassword', 'password', [
                'label' => 'sylius.form.user.password.label',
            ])
            ->add('enabled', 'checkbox', [
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

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return sprintf('%s_%s', $this->metadata->getApplicationName(), $this->metadata->getName());
    }
}
