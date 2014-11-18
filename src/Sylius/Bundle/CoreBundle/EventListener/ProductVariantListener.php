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

use Doctrine\Common\Persistence\Event\PreUpdateEventArgs;
use Sylius\Bundle\CoreBundle\Mailer\ProductChangeMailerInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Model\WishlistItemInterface;
use Sylius\Component\Core\Repository\WishlistRepositoryInterface;

/**
 * Sends email notification when product changes.
 */
class ProductVariantListener
{
    /**
     * @var WishlistRepositoryInterface
     */
    protected $wishlistRepository;

    /**
     * @var ProductChangeMailerInterface
     */
    protected $mailer;

    public function __construct(WishlistRepositoryInterface $wishlistRepository, ProductChangeMailerInterface $mailer)
    {
        $this->wishlistRepository = $wishlistRepository;
        $this->mailer = $mailer;
    }

    public function preUpdate(PreUpdateEventArgs $eventArgs)
    {
        $entity = $eventArgs->getObject();
        if ($entity instanceof ProductVariantInterface) {
            if ($eventArgs->hasChangedField('price')) {
                $oldPrice = $eventArgs->getOldValue('price');
                $newPrice = $eventArgs->getNewValue('price');

                foreach ($this->wishlistRepository->findEmailsByNotification(WishlistItemInterface::NOTIFY_ON_PRICE_CHANGE) as $email) {
                    $this->mailer->sendPriceChange($entity, $oldPrice, $newPrice, $email);
                }
            }

            if ($eventArgs->hasChangedField('onHand') && 0 < $eventArgs->getNewValue('onHand')) {
                foreach ($this->wishlistRepository->findEmailsByNotification(WishlistItemInterface::NOTIFY_ON_STOCK_CHANGE) as $email) {
                    $this->mailer->sendStockChange($entity, $email);
                }
            }
        }
    }
}
