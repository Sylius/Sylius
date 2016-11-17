<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Form\Type\Promotion\Rule;

use Sylius\Bundle\ResourceBundle\Form\DataTransformer\ResourceToIdentifierTransformer;
use Sylius\Bundle\ResourceBundle\Form\Type\ResourceChoiceType;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\ReversedTransformer;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;

/**
 * @author Antonio Perić <antonio@locastic.com>
 */
class CustomerGroupType extends AbstractType
{
    /**
     * @var RepositoryInterface
     */
    protected $groupRepository;

    /**
     * @param RepositoryInterface $groupRepository
     */
    public function __construct(RepositoryInterface $groupRepository)
    {
        $this->groupRepository = $groupRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                $builder->create('group', ResourceChoiceType::class, [
                    'resource' => 'sylius.customer_group',
                    'label' => 'sylius.form.promotion_action.customer_group',
                    'property' => 'name',
                    'class' => $this->groupRepository->getClassName(),
                    'constraints' => [
                        new NotBlank(),
                        new Type(['type' => 'numeric']),
                    ],
                ])->addModelTransformer(new ReversedTransformer(new ResourceToIdentifierTransformer($this->groupRepository, 'id')))
            )
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'sylius_promotion_rule_customer_group_configuration';
    }
}
