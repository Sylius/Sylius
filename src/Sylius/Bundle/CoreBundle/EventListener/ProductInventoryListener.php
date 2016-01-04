<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\EventListener;

use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Inventory\Factory\StockItemFactoryInterface;
use Sylius\Component\Resource\Exception\UnexpectedTypeException;
use Symfony\Component\EventDispatcher\GenericEvent;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class ProductInventoryListener
{
    private $stockItemFactory;
    private $manager;

    public function __construct(StockItemFactoryInterface $stockItemFactory, ObjectManager $manager)
    {
        $this->stockItemFactory = $stockItemFactory;
        $this->manager = $manager;
    }

    public function createProductStockItems(GenericEvent $event)
    {
        $product = $event->getSubject();

        if (!$product instanceof ProductInterface) {
            throw new UnexpectedTypeException(
                $product,
                'Sylius\Component\Core\Model\ProductInterface'
            );
        }

        $this->stockItemFactory->createAllForStockable($product->getMasterVariant());

        $this->manager->persist($product);
        $this->manager->flush();
    }

    public function createProductVariantStockItems(GenericEvent $event)
    {
        $productVariant = $event->getSubject();

        if (!$productVariant instanceof ProductVariantInterface) {
            throw new UnexpectedTypeException(
                $product,
                'Sylius\Component\Core\Model\VariantProductInterface'
            );
        }

        $this->stockItemFactory->createAllForStockable($productVariant);

        $this->manager->persist($productVariant);
        $this->manager->flush();
    }
}
