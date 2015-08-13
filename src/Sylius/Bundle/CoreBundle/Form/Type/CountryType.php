<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Form\Type;

use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Sylius\Component\Addressing\Model\CountryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Intl\Intl;

/**
 * @author Paweł Jędrzejewski <pjedrzejewski@sylius.pl>
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 * @author Gustavo Perdomo <gperdomor@gmail.com>
 */
class CountryType extends AbstractResourceType
{
    /**
     * @var RepositoryInterface
     */
    private $countryRepository;

    /**
     * {@inheritdoc}
     *
     * @param RepositoryInterface $countryRepository
     */
    public function __construct($dataClass, array $validationGroups = array(), RepositoryInterface $countryRepository)
    {
        $this->countryRepository = $countryRepository;

        parent::__construct($dataClass, $validationGroups);
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $self = $this;
        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) use ($self) {
                // Adding dynamically created isoName field
                $nameOptions = array(
                    'label' => 'sylius.form.country.name',
                );

                $country = $event->getData();
                if ($country instanceof CountryInterface && null !== $country->getIsoName()) {
                    $nameOptions['disabled'] = true;
                } else {
                    $nameOptions['choices'] = $self->getAvailableCountries();
                }

                $form = $event->getForm();
                $form->add('isoName', 'country', $nameOptions);
            }
        );

        $builder
            ->add('provinces', 'collection', array(
                'type' => 'sylius_province',
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'button_add_label' => 'sylius.country.add_province',
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_country';
    }

    /**
     * Should be private, used public to support PHP 5.3
     *
     * @internal
     *
     * @return array
     */
    public function getAvailableCountries()
    {
        $availableCountries = Intl::getRegionBundle()->getCountryNames();

        /** @var CountryInterface[] $definedCountries */
        $definedCountries = $this->countryRepository->findAll();
        foreach ($definedCountries as $country) {
            unset($availableCountries[$country->getIsoName()]);
        }

        return $availableCountries;
    }
}
