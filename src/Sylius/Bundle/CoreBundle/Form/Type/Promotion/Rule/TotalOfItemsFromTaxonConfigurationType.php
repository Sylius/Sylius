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

use Sylius\Bundle\MoneyBundle\Form\Type\MoneyType;
use Sylius\Bundle\ResourceBundle\Form\DataTransformer\ResourceToIdentifierTransformer;
use Sylius\Bundle\ResourceBundle\Form\Type\ResourceAutocompleteChoiceType;
use Sylius\Bundle\TaxonomyBundle\Form\Type\TaxonAutocompleteChoiceType;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\ReversedTransformer;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
final class TotalOfItemsFromTaxonConfigurationType extends AbstractType
{
    /**
     * @var RepositoryInterface
     */
    private $taxonRepository;

    /**
     * @param RepositoryInterface $taxonRepository
     */
    public function __construct(RepositoryInterface $taxonRepository)
    {
        $this->taxonRepository = $taxonRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('taxon', TaxonAutocompleteChoiceType::class, [
                'label' => 'sylius.form.promotion_rule.total_of_items_from_taxon.taxon',
            ])
            ->add('amount', MoneyType::class, [
                'label' => 'sylius.form.promotion_rule.total_of_items_from_taxon.amount',
                'currency' => $options['currency'],
            ])
        ;

        $builder->get('taxon')->addModelTransformer(new ReversedTransformer(new ResourceToIdentifierTransformer($this->taxonRepository, 'code')));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setRequired('currency')
            ->setAllowedTypes('currency', 'string')
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'sylius_promotion_rule_total_of_items_from_taxon_configuration';
    }
}
