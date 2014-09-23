<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Form\Type\Pricing;

use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Group based pricing configuration form type.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class ZoneBasedConfigurationType extends AbstractType
{
    protected $zoneRepository;

    /**
     * @param RepositoryInterface $zoneRepository
     */
    public function __construct(RepositoryInterface $zoneRepository)
    {
        $this->zoneRepository = $zoneRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if (null !== $options['scope']) {
            $zones = $this->zoneRepository->findBy(array('scope' => $options['scope']));
        } else {
            $zones = $this->zoneRepository->findAll();
        }

        foreach ($zones as $zone) {
            $builder
                ->add($zone->getId(), 'sylius_money', array(
                    'label'    => $zone->getName(),
                    'required' => false,
                ))
            ;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver
            ->setDefaults(array(
                'data_class' => null,
                'scope'      => null
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_price_calculator_zone_based';
    }
}
