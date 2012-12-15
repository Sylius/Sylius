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
use Sylius\Bundle\TaxonomiesBundle\Form\DataTransformer\SelectionToTaxonsTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\ChoiceList\ObjectChoiceList;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Taxonomies selection form.
 * It creates one select form for each taxonomy.
 * Transforms it into collection of taxons.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class SelectionType extends AbstractType
{
    protected $repository;

    public function __construct(ObjectRepository $repository)
    {
        $this->repository = $repository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $taxonomies = $this->repository->findAll();

        $builder->addModelTransformer(new SelectionToTaxonsTransformer($taxonomies));

        foreach ($taxonomies as $taxonomy) {
            $builder->add(strtolower($taxonomy->getName()), 'choice', array(
                'choice_list' => new ObjectChoiceList($taxonomy->getTaxons()),
                'multiple'    => $options['multiple'],
                'label'       => $taxonomy->getName()
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
                'data_class'   => null,
                'multiple'     => true,
                'render_label' => false
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_taxonomies_selection';
    }
}
