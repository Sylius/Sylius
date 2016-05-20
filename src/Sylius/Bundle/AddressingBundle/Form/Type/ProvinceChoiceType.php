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
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class ProvinceChoiceType extends AbstractType
{
    /**
     * @var RepositoryInterface
     */
    protected $provinceRepository;

    /**
     * @param RepositoryInterface $provinceRepository
     */
    public function __construct(RepositoryInterface $provinceRepository)
    {
        $this->provinceRepository = $provinceRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $choices = function (Options $options) {
            if (null === $options['country']) {
                $choices = $this->provinceRepository->findAll();
            } else {
                $choices = $options['country']->getProvinces();
            }

            return new ArrayChoiceList($choices);
        };

        $resolver
            ->setDefaults([
                'choice_translation_domain' => false,
                'choice_list' => $choices,
                'country' => null,
                'label' => 'sylius.form.address.province',
                'empty_value' => 'sylius.form.province.select',
            ])
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
        return 'sylius_province_choice';
    }
}
