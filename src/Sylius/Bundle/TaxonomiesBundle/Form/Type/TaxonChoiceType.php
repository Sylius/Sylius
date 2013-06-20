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

/**
 * Taxon choice form form.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class TaxonChoiceType extends AbstractType
{
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
        $choiceList = function (Options $options) {
            $taxon = $options['taxonomy']->getTaxonsAsList();

            if (null !== $options['filter']) {
                $taxon = $taxon->filter($options['filter']);
            }

            return new ObjectChoiceList($taxon);
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
