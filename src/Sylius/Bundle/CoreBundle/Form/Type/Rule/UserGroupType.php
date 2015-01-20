<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Form\Type\Rule;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Sylius\Bundle\CoreBundle\Doctrine\ORM\GroupRepository;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;

/**
 * UserGroup rule configuration form type.
 *
 * @author Antonio Perić <antonio@locastic.com>
 */
class UserGroupType extends AbstractType
{
    protected $validationGroups;

    /**
     * @var GroupRepository
     */
    protected $groupRepository;

    public function __construct(array $validationGroups, GroupRepository $groupRepository)
    {
        $this->validationGroups = $validationGroups;
        $this->groupRepository = $groupRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $groupRepository = $this->groupRepository;

        $builder
            ->add(
                'groups',
                'sylius_entity_to_identifier',
                array(
                    'label' => 'sylius.form.action.user_group',
                    'property' => 'name',
                    'class' => $groupRepository->getClassName(),
                    'query_builder' => function () use ($groupRepository) {
                        return $groupRepository->getFormQueryBuilder();
                    },
                    'constraints' => array(
                        new NotBlank(),
                        new Type(array('type' => 'numeric')),
                    ),
                )
            );
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setDefaults(
                array(
                    'validation_groups' => $this->validationGroups,
                )
            );
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_promotion_rule_user_group_configuration';
    }
}
