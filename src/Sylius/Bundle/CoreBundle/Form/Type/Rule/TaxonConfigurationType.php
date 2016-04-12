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
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class TaxonConfigurationType extends AbstractType
{
    /**
     * @var DataTransformerInterface
     */
    private $taxonsToCodesTransformer;

    /**
     * @param DataTransformerInterface $taxonsToCodesTransformer
     */
    public function __construct(DataTransformerInterface $taxonsToCodesTransformer)
    {
        $this->taxonsToCodesTransformer = $taxonsToCodesTransformer;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('taxons', 'sylius_taxon_choice', [
                'label' => 'sylius.form.promotion_rule.taxon.taxons',
                'multiple' => true,
            ])
            ->addModelTransformer($this->taxonsToCodesTransformer)
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_promotion_rule_taxon_configuration';
    }
}
