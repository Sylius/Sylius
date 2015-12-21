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

use Sylius\Component\Addressing\Model\AdministrativeAreaInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
class AdministrativeAreaCodeChoiceType extends AdministrativeAreaChoiceType
{
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $choices = function (Options $options) {
            if (null === $options['country']) {
                $administrativeAreas = $this->administrativeAreaRepository->findAll();
            } else {
                $administrativeAreas = $options['country']->getAdministrativeAreas();
            }

            return $this->getAdministrativeAreasCodes($administrativeAreas);
        };

        $resolver->setDefault('choice_list', null);
        $resolver->setDefault('choices', $choices);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_administrative_area_code_choice';
    }

    /**
     * @param AdministrativeAreaInterface[] $administrativeAreas
     *
     * @return array
     */
    private function getAdministrativeAreasCodes(array $administrativeAreas)
    {
        $areasCodes = array();

        /** @var AdministrativeAreaInterface $area */
        foreach ($administrativeAreas as $area) {
            $areasCodes[$area->getCode()] = $area->getName();
        }

        return $areasCodes;
    }
}
