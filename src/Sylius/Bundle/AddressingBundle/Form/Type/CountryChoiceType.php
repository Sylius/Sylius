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

use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\ChoiceList\ObjectChoiceList;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class CountryChoiceType extends AbstractType
{
    /**
     * @var RepositoryInterface
     */
    protected $countryRepository;

    /**
     * @param RepositoryInterface $repository
     */
    public function __construct(RepositoryInterface $repository)
    {
        $this->countryRepository = $repository;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $choiceList = function (Options $options)  {
            if (null === $options['enabled']) {
                $choices = $this->countryRepository->findAll();
            } else {
                $choices = $this->countryRepository->findBy(array('enabled' => $options['enabled']));
            }

            return new ObjectChoiceList($choices, null, array(), null, 'id');
        };

        $resolver
            ->setDefaults(array(
                'choice_list' => $choiceList,
                'enabled'     => null,
                'label'       => 'sylius.form.address.country',
                'empty_value' => 'sylius.form.country.select',
            ))
        ;
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
    public function getName()
    {
        return 'sylius_country_choice';
    }
}
