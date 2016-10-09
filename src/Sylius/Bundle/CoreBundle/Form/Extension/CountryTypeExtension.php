<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Form\Extension;

use Sylius\Bundle\AddressingBundle\Form\Type\CountryType;
use Sylius\Bundle\ResourceBundle\Form\EventSubscriber\AddCodeFormSubscriber;
use Sylius\Component\Addressing\Model\CountryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Intl\Intl;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 * @author Gustavo Perdomo <gperdomor@gmail.com>
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
final class CountryTypeExtension extends AbstractTypeExtension
{
    /**
     * @var RepositoryInterface
     */
    private $countryRepository;

    /**
     * @param RepositoryInterface $countryRepository
     */
    public function __construct(RepositoryInterface $countryRepository)
    {
        $this->countryRepository = $countryRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('enabled', 'checkbox', [
                'label' => 'sylius.form.country.enabled',
            ])
        ;

        $builder->getEventDispatcher()->removeSubscriber(new AddCodeFormSubscriber());

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            // Adding dynamically created isoName field
            $nameOptions = [
                'label' => 'sylius.form.country.name',
            ];

            $country = $event->getData();
            if ($country instanceof CountryInterface && null !== $country->getCode()) {
                $nameOptions['disabled'] = true;
                $nameOptions['choices'] = [
                    $country->getCode() => $this->getCountryName($country->getCode())
                ];
            } else {
                $nameOptions['choices'] = $this->getAvailableCountries();
            }

            $nameOptions['choices_as_values'] = false;

            $form = $event->getForm();
            $form->add('code', 'country', $nameOptions);
        });
    }

    /**
     * {@inheritdoc}
     */
    public function getExtendedType()
    {
        return CountryType::class;
    }

    /**
     * @param $code
     *
     * @return null|string
     */
    private function getCountryName($code)
    {
        return Intl::getRegionBundle()->getCountryName($code);
    }

    /**
     * @return array
     */
    private function getAvailableCountries()
    {
        $availableCountries = Intl::getRegionBundle()->getCountryNames();

        /** @var CountryInterface[] $definedCountries */
        $definedCountries = $this->countryRepository->findAll();

        foreach ($definedCountries as $country) {
            unset($availableCountries[$country->getCode()]);
        }

        return $availableCountries;
    }
}
