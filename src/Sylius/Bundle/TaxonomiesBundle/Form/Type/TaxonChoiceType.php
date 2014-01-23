<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\TaxonomiesBundle\Form\Type;

use Symfony\Bridge\Doctrine\Form\DataTransformer\CollectionToArrayTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\ChoiceList\ObjectChoiceList;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Sylius\Bundle\TaxonomiesBundle\Repository\TaxonRepositoryInterface;

/**
 * Taxon choice form form.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class TaxonChoiceType extends AbstractType
{
    /**
     * Taxonon repository.
     *
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
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $repository = $this->taxonRepository;
        $choiceList = function (Options $options) use ($repository) {
            $taxons = $repository->getTaxonsAsList($options['taxonomy']);

            if (null !== $options['filter']) {
                $taxons = array_filter($taxons, $options['filter']);
            }

            return new ObjectChoiceList($taxons);
        };

        $resolver
            ->setDefaults(array(
                'choice_list' => $choiceList
            ))
            ->setRequired(array(
                'taxonomy',
                'filter'
            ))
            ->setAllowedTypes(array(
                'taxonomy' => array('Sylius\Bundle\TaxonomiesBundle\Model\TaxonomyInterface'),
                'filter' => array('\Closure', 'null')
            ))
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
