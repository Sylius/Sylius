<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\TaxonomyBundle\Form\Type;

use Sylius\Component\Taxonomy\Model\TaxonomyInterface;
use Sylius\Component\Taxonomy\Repository\TaxonRepositoryInterface;
use Symfony\Bridge\Doctrine\Form\DataTransformer\CollectionToArrayTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\ChoiceList\ObjectChoiceList;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Saša Stamenković <umpirsky@gmail.com>
 * @author Anna Walasek <anna.walasek@lakion.com>
 */
class TaxonChoiceType extends AbstractType
{
    /**
     * @var TaxonRepositoryInterface
     */
    protected $taxonRepository;

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
        if ($options['multiple']) {
            $builder->addModelTransformer(new CollectionToArrayTransformer());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'choice';
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $repository = $this->taxonRepository;
        $choiceList = function (Options $options) use ($repository) {
            $taxons = $repository->getNonRootTaxons();

            if (null !== $options['taxonomy']) {
                $taxons = $repository->getTaxonsAsList($options['taxonomy']);
            }

            if (null !== $options['filter']) {
                $taxons = array_filter($taxons, $options['filter']);
            }

            return new ObjectChoiceList($taxons, null, [], 'taxonomy', 'id');
        };

        $resolver
            ->setDefaults([
                'choice_list' => $choiceList,
                'taxonomy' => null,
                'filter' => null,
            ])
            ->setAllowedTypes('taxonomy', [TaxonomyInterface::class, 'null'])
            ->setAllowedTypes('filter', ['callable', 'null'])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_taxon_choice';
    }
}
