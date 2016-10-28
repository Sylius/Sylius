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

use Sylius\Component\Addressing\Model\AddressInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
class AddressChoiceType extends AbstractType
{
    /**
     * @var RepositoryInterface
     */
    private $addressRepository;

    /**
     * @param RepositoryInterface $addressRepository
     */
    public function __construct(RepositoryInterface $addressRepository)
    {
        $this->addressRepository = $addressRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $choices = function (Options $options) {
            if (null === $options['customer']) {
                return $this->addressRepository->findAll();
            }

            return $this->addressRepository->findBy(['customer' => $options['customer']]);
        };

        $resolver->setDefaults([
            'class' => AddressInterface::class,
            'choices' => $choices,
            'customer' => null,
            'label' => false,
            'placeholder' => false,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return ChoiceType::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_address_choice';
    }
}
