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

use Sylius\Bundle\CoreBundle\Repository\VariantRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ContainItemConfigurationType extends AbstractType
{
    /**
     * @var array
     */
    protected $validationGroups;

    /**
     * @var string
     */
    protected $dataClass;

    /**
     * @param array  $validationGroups Array of validation groups
     * @param string $dataClass        Class of Product Variant model
     */
    public function __construct(array $validationGroups, $dataClass)
    {
        $this->validationGroups = $validationGroups;
        $this->dataClass        = $dataClass;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('variants', 'sylius_entity_to_identifier', array(
                'label'      => 'sylius.form.action.contain_item_configuration.variant',
                'class'      => $this->dataClass,
                'identifier' => 'id'
            ))
            ->add('only', 'checkbox', array(
                'label'    => 'sylius.form.rule.contain_item_configuration.only',
            ))
            ->add('exclude', 'checkbox', array(
                'label'    => 'sylius.form.rule.contain_item_configuration.exclude',
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'validation_groups' => $this->validationGroups,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_promotion_rule_contain_item_configuration';
    }
}
