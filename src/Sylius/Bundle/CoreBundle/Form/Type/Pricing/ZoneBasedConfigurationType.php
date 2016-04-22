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
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class ZoneBasedConfigurationType extends AbstractType
{
    /**
     * @var RepositoryInterface
     */
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
        foreach ($this->getZones($options) as $zone) {
            $builder
                ->add($zone->getId(), 'sylius_money', [
                    'label' => $zone->getName(),
                    'required' => false,
                ])
            ;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'data_class' => null,
                'scope' => null,
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_price_calculator_zone_based';
    }

    /**
     * @param array $options
     *
     * @return array
     */
    private function getZones(array $options)
    {
        if (isset($options['scope'])) {
            return $this->zoneRepository->findBy(['scope' => $options['scope']]);
        }

        return $this->zoneRepository->findAll();
    }
}
