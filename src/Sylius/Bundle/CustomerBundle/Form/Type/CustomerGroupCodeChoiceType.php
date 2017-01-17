<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CustomerBundle\Form\Type;

use Sylius\Bundle\ResourceBundle\Form\DataTransformer\ResourceToIdentifierTransformer;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\ReversedTransformer;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class CustomerGroupCodeChoiceType extends AbstractType
{
    /**
     * @var RepositoryInterface
     */
    private $customerGroupRepository;

    /**
     * @param RepositoryInterface $customerGroupRepository
     */
    public function __construct(RepositoryInterface $customerGroupRepository)
    {
        $this->customerGroupRepository = $customerGroupRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer(new ReversedTransformer(new ResourceToIdentifierTransformer($this->customerGroupRepository, 'code')));
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return CustomerGroupChoiceType::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'sylius_customer_group_code_choice';
    }
}
