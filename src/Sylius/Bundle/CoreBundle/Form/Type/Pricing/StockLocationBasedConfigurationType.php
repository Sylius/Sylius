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

use Sylius\Component\Inventory\Repository\StockLocationRepositoryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * StockLocation based pricing configuration form type.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class StockLocationBasedConfigurationType extends AbstractType
{
    protected $stockLocationRepository;

    public function __construct(StockLocationRepositoryInterface $stockLocationRepository)
    {
        $this->stockLocationRepository = $stockLocationRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        foreach ($this->stockLocationRepository->findAllEnabled() as $stockLocation) {
            $builder
                ->add($stockLocation->getId(), 'sylius_money', array(
                    'label'    => $stockLocation->getName(),
                    'required' => false,
                ))
            ;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_price_calculator_stock_location_based';
    }
}
