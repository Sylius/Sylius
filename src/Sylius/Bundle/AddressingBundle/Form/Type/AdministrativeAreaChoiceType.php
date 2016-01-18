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

use Sylius\Component\Addressing\Model\CountryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\ChoiceList\ArrayChoiceList;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Paweł Jędrzejewski <pjedrzejewski@sylius.pl>
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
class AdministrativeAreaChoiceType extends AbstractType
{
    /**
     * @var RepositoryInterface
     */
    protected $administrativeAreaRepository;

    /**
     * @param RepositoryInterface $administrativeAreaRepository
     */
    public function __construct(RepositoryInterface $administrativeAreaRepository)
    {
        $this->administrativeAreaRepository = $administrativeAreaRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $choices = function (Options $options) {
            if (null === $options['country']) {
                $choices = $this->administrativeAreaRepository->findAll();
            } else {
                $choices = $options['country']->getAdministrativeAreas();
            }

            return new ArrayChoiceList($choices);
        };

        $resolver
            ->setDefaults(array(
                'choice_list' => $choices,
                'country' => null,
                'label' => 'sylius.form.address.administrative_area',
                'empty_value' => 'sylius.form.administrative_area.select',
            ))
        ;
        $resolver->addAllowedTypes('country', 'NULL');
        $resolver->addAllowedTypes('country', CountryInterface::class);
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
        return 'sylius_administrative_area_choice';
    }
}
