<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\DataFixtures\ORM;

use Nelmio\Alice\ProcessorInterface;
use Sylius\Bundle\CoreBundle\Model\OrderInterface;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Symfony\Component\Intl\Intl;

/**
 * Order processor : adds items to the order and its shipment, calculates its amount
 *
 * @author Julien Janvier <j.janvier@gmail.com>
 */
class OrderProcessor implements ProcessorInterface
{
    private $orderItemRepository;
    private $variantRepository;
    private $variants;
    private $nbVariants;

    public function __construct(EntityRepository $orderItemRepository, EntityRepository $variantRepository)
    {
        $this->orderItemRepository = $orderItemRepository;
        $this->variantRepository = $variantRepository;
        $this->variants = $variantRepository->findAll();
        $this->nbVariants = count($this->variants);
    }

    public function preProcess($order)
    {
        if ($order instanceof OrderInterface) {
            for ($i = 0; $i <= rand(3, 6); $i++) {
                $item = $this->orderItemRepository->createNew();
                $variant = $this->variants[rand(0, $this->nbVariants - 1)];

                $item->setVariant($variant);
                $item->setUnitPrice($variant->getPrice());
                $item->setQuantity(rand(1, 5));

                $order->addItem($item);
            }

            foreach ($order->getInventoryUnits() as $item) {
                $order->getShipments()->first()->addItem($item);
            }

            $order->calculateTotal();
            $order->complete();
        }
    }

    public function postProcess($order)
    {
        return;
    }
}