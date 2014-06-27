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

use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Resource\Exception\UnexpectedTypeException;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * Product update listener.
 *
 * @author Alex Demchenko <pilo.uanic@gmail.com>
 */
class ProductUpdateListener
{
    /**
     * @var ObjectManager
     */
    protected $manager;

    public function __construct(ObjectManager $manager)
    {
        $this->manager = $manager;
    }

    public function productUpdate(GenericEvent $event)
    {
        $product = $event->getSubject();
        if (!$product instanceof ProductInterface) {
            throw new UnexpectedTypeException($product, 'Sylius\Component\Core\Model\ProductInterface');
        }

        $product->setAvailableOn($product->getMasterVariant()->getAvailableOn());

        $this->manager->persist($product);
        $this->manager->flush($product);
    }
}
