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

use Sylius\Bundle\UserBundle\Doctrine\ORM\GroupRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;

/**
 * @author Antonio Perić <antonio@locastic.com>
 */
class CustomerGroupType extends AbstractType
{
<<<<<<< HEAD
    /**
     * @var string[]
     */
    protected $validationGroups;

    /**
     * @var GroupRepository
     */
    protected $groupRepository;

    /**
     * @param string[]        $validationGroups
     * @param GroupRepository $groupRepository
     */
    public function __construct(array $validationGroups, GroupRepository $groupRepository)
    {
        $this->validationGroups = $validationGroups;
        $this->groupRepository = $groupRepository;
    }

=======
>>>>>>> Fix specs
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
<<<<<<< HEAD
            ->add('groups', 'sylius_entity_to_identifier', array(
                'label' => 'sylius.form.action.customer_group',
                'property' => 'name',
                'class' => $this->groupRepository->getClassName(),
                'query_builder' => function () {
                    return $this->groupRepository->getFormQueryBuilder();
                },
=======
            ->add('groups', 'sylius_group_to_identifier', array(
                'label' => 'sylius.form.action.customer_group',
>>>>>>> Fix specs
                'constraints' => array(
                    new NotBlank(),
                    new Type(array('type' => 'numeric')),
                ),
            ))
        ;
<<<<<<< HEAD
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults(array(
                'validation_groups' => $this->validationGroups,
            ))
        ;
=======
>>>>>>> Fix specs
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_promotion_rule_customer_group_configuration';
    }
}
