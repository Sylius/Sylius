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

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\ChoiceList\ObjectChoiceList;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Base province choice type.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@sylius.pl>
 */
class ProvinceChoiceType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $choiceList = function (Options $options) {
            return new ObjectChoiceList($options['country']->getProvinces());
        };

        $resolver
            ->setDefaults(array(
                'choice_list' => $choiceList
            ))
            ->setRequired(array(
                'country'
            ))
            ->setAllowedTypes(array(
                'country' => array('Sylius\Bundle\AddressingBundle\Model\CountryInterface')
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
        return 'sylius_addressing_province_choice';
    }
}
