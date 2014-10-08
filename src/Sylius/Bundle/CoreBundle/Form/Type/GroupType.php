<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Form\Type;

use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * User group form type.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class GroupType extends AbstractResourceType
{
    /**
     * @var string[]
     */
    protected $roles;

    public function __construct($dataClass, array $validationGroups = array(), array $roles)
    {
        parent::__construct($dataClass, $validationGroups);

        $this->roles = $roles;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text', array(
                'label'    => 'sylius.form.group.name',
            ))
            ->add('roles', 'choice', array(
                'label'    => 'sylius.form.group.roles',
                'multiple' => true,
                'choices'  => $this->parseRoles(),
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_group';
    }

    /**
     * @return string[]
     */
    private function parseRoles()
    {
        $choices = array();
        foreach ($this->roles as $key => $roles) {
            if (is_array($roles)) {
                foreach ($roles as $role) {
                    if ('ROLE_USER' === $role) {
                        continue;
                    }

                    if (!isset($choices[$role])) {
                        $choices[$key][$role] = $role;
                    }
                }
            } else {
                $choices[$roles] = $roles;
            }
        }

        foreach (array('ROLE_SYLIUS_ADMIN', 'ROLE_SYLIUS_SUPER_ADMIN') as $role) {
            $choices[$role] = array_merge($choices[$role], array($role => $role));
        }

        return $choices;
    }
}
