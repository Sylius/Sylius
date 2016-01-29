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

use Sylius\Component\Addressing\Model\ProvinceInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
class ProvinceCodeChoiceType extends ProvinceChoiceType
{
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $choices = function (Options $options) {
            if (null === $options['country']) {
                $provinces = $this->provinceRepository->findAll();
            } else {
                $provinces = $options['country']->getProvinces();
            }

            return $this->getProvinceCodes($provinces);
        };

        $resolver->setDefault('choice_list', null);
        $resolver->setDefault('choices', $choices);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_province_code_choice';
    }

    /**
     * @param ProvinceInterface[] $provinces
     *
     * @return array
     */
    private function getProvinceCodes($provinces)
    {
        $provincesCodes = [];

        /* @var ProvinceInterface $province */
        foreach ($provinces as $province) {
            $provincesCodes[$province->getCode()] = $province->getName();
        }

        return $provincesCodes;
    }
}
