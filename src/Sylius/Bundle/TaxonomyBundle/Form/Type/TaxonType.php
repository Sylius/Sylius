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

use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Sylius\Bundle\TaxonomyBundle\Form\EventListener\BuildTaxonFormListener;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Taxon form type.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class TaxonType extends AbstractResourceType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text', array(
                'label' => 'sylius.form.taxon.name'
            ))
            ->add('permalink', 'text', array(
                'required' => false,
                'label' => 'sylius.form.taxon.permalink'
            ))
            ->add('description', 'text', array(
                'required' => false,
                'label' => 'sylius.form.taxon.description'
            ))
            ->addEventSubscriber(new BuildTaxonFormListener($builder->getFormFactory()))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_taxon';
    }
}
