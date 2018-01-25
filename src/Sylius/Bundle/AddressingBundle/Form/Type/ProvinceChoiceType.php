<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\AddressingBundle\Form\Type;

use Sylius\Component\Addressing\Model\CountryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ProvinceChoiceType extends AbstractType
{
    /**
     * @var RepositoryInterface
     */
    private $provinceRepository;

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
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'choices' => function (Options $options): iterable {
                if (null === $options['country']) {
                    return $this->provinceRepository->findAll();
                }

                return $options['country']->getProvinces();
            },
            'choice_value' => 'code',
            'choice_label' => 'name',
            'choice_translation_domain' => false,
            'country' => null,
            'label' => 'sylius.form.address.province',
            'placeholder' => 'sylius.form.province.select',
        ]);
        $resolver->addAllowedTypes('country', ['null', CountryInterface::class]);
    }

    /**
     * {@inheritdoc}
     */
    public function getParent(): string
    {
        return ChoiceType::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(): string
    {
        return 'sylius_province_choice';
    }
}
