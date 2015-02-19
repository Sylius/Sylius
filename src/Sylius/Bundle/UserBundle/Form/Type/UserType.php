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
use Sylius\Bundle\UserBundle\Form\EventListener\CanonicalizerFormListener;
use Sylius\Component\User\Canonicalizer\CanonicalizerInterface;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class UserType extends AbstractResourceType
{
    /**
     * DataFetcher registry.
     *
     * @var CanonicalizerInterface
     */
    protected $canonicalizer;

    /**
    * Constructor.
    *
    * @param CanonicalizerInterface $canonicalizer
    */
    public function __construct($dataClass, array $validationGroups, CanonicalizerInterface $canonicalizer)
    {
        parent::__construct($dataClass, $validationGroups);
        $this->canonicalizer = $canonicalizer;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->addEventSubscriber(new CanonicalizerFormListener($this->canonicalizer))
            ->add('firstName', 'text', array(
                'label' => 'sylius.form.user.first_name',
            ))
            ->add('lastName', 'text', array(
                'label' => 'sylius.form.user.last_name',
            ))
            ->add('email', 'text', array(
                'label' => 'sylius.form.user.email',
            ))
            ->add('plainPassword', 'password', array(
                'label' => 'sylius.form.user.password.label',
            ))
            ->add('enabled', 'checkbox', array(
                'label' => 'sylius.form.user.enabled',
            ))
            ->add('groups', 'sylius_group_choice', array(
                'label'    => 'sylius.form.user.groups',
                'multiple' => true,
                'required' => false,
            ))
            ->add('authorizationRoles', 'sylius_role_choice', array(
                'label'    => 'sylius.form.user.roles',
                'multiple' => true,
                'expanded' => true,
                'required' => false
            ))
            ->remove('username')
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_user';
    }
}
