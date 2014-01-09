<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\AddressingBundle\Form\Type;

use Doctrine\Common\Persistence\ObjectRepository;
use Sylius\Bundle\AddressingBundle\Form\DataTransformer\CountryToIdentifierTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Country to identifier type.
 *
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class CountryToIdentifierType extends AbstractType
{
    private $countryRepository;

    public function __construct(ObjectRepository $countryRepository)
    {
        $this->countryRepository = $countryRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer(new CountryToIdentifierTransformer($this->countryRepository, $options['identifier']));
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setDefaults(array(
                'data_class' => null
            ))
            ->setRequired(array(
                'identifier'
            ))
            ->setAllowedTypes(array(
                'identifier' => array('string')
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'sylius_country_choice';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_country_to_identifier';
    }
}
