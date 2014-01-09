<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Form\Type\Action;

use Sylius\Bundle\ResourceBundle\Model\RepositoryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;

/**
 * Free product configuration form.
 *
 * @author Alexandre Bacco <alexandre.bacco@gmail.com>
 */
class AddProductConfigurationType extends AbstractType
{
    protected $validationGroups;

    /**
     * Variant repository.
     *
     * @var RepositoryInterface
     */
    protected $variantRepository;

    public function __construct(array $validationGroups, RepositoryInterface $variantRepository)
    {
        $this->validationGroups = $validationGroups;
        $this->variantRepository = $variantRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('variant', 'choice', array(
                'label' => 'sylius.form.action.add_product_configuration.variant',
                'choices' => $this->getVariantsList(),
                'constraints' => array(
                    new NotBlank(),
                    new Type(array('type' => 'numeric')),
                )
            ))
            ->add('quantity', 'integer', array(
                'label' => 'sylius.form.action.add_product_configuration.quantity',
                'data'  => 1,
                'constraints' => array(
                    new NotBlank(),
                    new Type(array('type' => 'numeric')),
                )
            ))
            ->add('quantity', 'integer', array(
                'label' => 'sylius.form.action.add_product_configuration.price',
                'data'  => 0,
                'constraints' => array(
                    new NotBlank(),
                    new Type(array('type' => 'numeric')),
                )
            ))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setDefaults(array(
                'validation_groups' => $this->validationGroups,
            ))
        ;
    }

    public function getName()
    {
        return 'sylius_promotion_action_add_product_configuration';
    }

    protected function getVariantsList()
    {
        $list = array();
        foreach ($this->variantRepository->findAll() as $variant) {
            $list[$variant->getId()] = (string) $variant;
        }

        return $list;
    }
}
