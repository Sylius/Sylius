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

use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\ChoiceList\ObjectChoiceList;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
class ZoneChoiceType extends AbstractType
{
    /**
     * @var RepositoryInterface
     */
    protected $repository;

    /**
     * @param RepositoryInterface $repository
     */
    public function __construct(RepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $choices = new ObjectChoiceList($this->getZones(), null, array(), null, 'code');

        $resolver
            ->setDefaults(array(
                'choice_list'  => $choices,
                'choice_label' => function (ZoneInterface $zone){
                    return $zone->getName();
                },
                'label'        => 'sylius.form.zone.types.zone',
                'empty_value'  => 'sylius.form.zone.select',
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
        return 'sylius_zone_choice';
    }

    /**
     * @return array
     */
    protected function getZones()
    {
        $zoneObjects = $this->repository->findAll();
        $zones = array();

        /* @var ZoneInterface $zone */
        foreach ($zoneObjects as $zone) {
            $zones[$zone->getCode()] = $zone;
        }

        return $zones;
    }
}
