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

use Sylius\Bundle\ResourceBundle\Model\RepositoryInterface;
use Sylius\Bundle\TaxonomiesBundle\Model\Taxonomy;
use Sylius\Bundle\TaxonomiesBundle\Repository\TaxonRepositoryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\ChoiceList\ObjectChoiceList;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use JMS\TranslationBundle\Annotation\Ignore;

/**
 * Taxon selection form.
 * It creates one select form for each taxonomy.
 * Transforms it into collection of taxons.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class TaxonSelectionType extends AbstractType
{
    /**
     * Taxonomies repository.
     *
     * @var RepositoryInterface
     */
    protected $taxonomyRepository;

    /**
     * Taxonon repository.
     *
     * @var TaxonRepositoryInterface
     */
    protected $taxonRepository;

    /**
     * Constructor.
     *
     * @param RepositoryInterface      $taxonomyRepository
     * @param TaxonRepositoryInterface $taxonRepository
     */
    public function __construct(RepositoryInterface $taxonomyRepository, TaxonRepositoryInterface $taxonRepository)
    {
        $this->taxonomyRepository = $taxonomyRepository;
        $this->taxonRepository = $taxonRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $taxonomies = $this->taxonomyRepository->findAll();

        $builder->addModelTransformer(new $options['model_transformer']($taxonomies));

        foreach ($taxonomies as $taxonomy) {
            /* @var $taxonomy Taxonomy*/
            $builder->add($taxonomy->getId(), 'choice', array(
                'choice_list' => new ObjectChoiceList($this->taxonRepository->getTaxonsAsList($taxonomy)),
                'multiple'    => $options['multiple'],
                'label'       => /** @Ignore */ $taxonomy->getName()
            ));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setDefaults(array(
                'data_class'         => null,
                'multiple'           => true,
                'render_label'       => false,
                'model_transformer'  => 'Sylius\Bundle\TaxonomiesBundle\Form\DataTransformer\TaxonSelectionToCollectionTransformer',
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_taxon_selection';
    }
}
