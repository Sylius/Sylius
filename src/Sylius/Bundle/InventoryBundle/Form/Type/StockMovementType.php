<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\InventoryBundle\Form\Type;

use Sylius\Component\Inventory\Model\StockableInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\ChoiceList\ArrayChoiceList;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class StockMovementType extends AbstractType
{
    private $stockableRepository;

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('stockable', 'choice', [
                'choice_list' => new ArrayChoiceList($this->stockableRepository->findAll()),
                'choice_label' => function ($stockable, $key, $index) {
                    return $stockable->__toString();
                },
            ])
            ->add('quantity', 'integer')
            ->add('location', 'sylius_stock_location_choice');
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setRequired([
                'stockable'
            ])
            ->setAllowedTypes([
                'stockable' => StockableInterface::class
            ]);
    }


    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_stock_movement';
    }

    public function setStockableRepository(RepositoryInterface $repository)
    {
        $this->stockableRepository = $repository;
    }
}
