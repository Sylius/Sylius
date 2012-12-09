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

use Doctrine\Common\Persistence\ObjectRepository;
use Symfony\Bridge\Doctrine\Form\DataTransformer\CollectionToArrayTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\ChoiceList\ObjectChoiceList;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Taxon choice form form.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class TaxonChoiceType extends AbstractType
{
    protected $repository;

    public function __construct(ObjectRepository $repository)
    {
        $this->repository = $repository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($options['multiple']) {
            $builder->addModelTransformer(new CollectionToArrayTransformer());
        }
    }

    public function getParent()
    {
        return 'choice';
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $taxons = $this->repository->findAll();
        $choiceList = new ObjectChoiceList($taxons);

        $resolver
            ->setDefaults(array(
                'choice_list' => $choiceList
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_taxonomies_taxon_choice';
    }
}
