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

use Sylius\Component\Taxonomy\Repository\TaxonRepositoryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class TotalOfItemsFromTaxonConfigurationType extends AbstractType
{
    /**
     * @var TaxonRepositoryInterface
     */
    private $taxonRepository;

    /**
     * @param TaxonRepositoryInterface $taxonRepository
     */
    public function __construct(TaxonRepositoryInterface $taxonRepository)
    {
        $this->taxonRepository = $taxonRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('taxon', 'sylius_entity_to_identifier', [
                'label' => 'sylius.form.promotion_rule.total_of_items_from_taxon.taxon',
                'class' => $this->taxonRepository->getClassName(),
                'query_builder' => function () {
                    return $this->taxonRepository->getFormQueryBuilder();
                },
                'identifier' => 'code',
            ])
            ->add('amount', 'sylius_money', [
                'label' => 'sylius.form.promotion_rule.total_of_items_from_taxon.amount',
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_promotion_rule_total_of_items_from_taxon_configuration';
    }
}
