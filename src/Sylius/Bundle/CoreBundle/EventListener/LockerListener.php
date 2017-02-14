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

use Doctrine\DBAL\LockMode;
use Doctrine\ORM\EntityManagerInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Product\Resolver\ProductVariantResolverInterface;
use Sylius\Component\Resource\Model\VersionedInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Webmozart\Assert\Assert;

/**
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
final class LockerListener
{
    /**
     * @var EntityManagerInterface
     */
    private $manager;

    /**
     * @var ProductVariantResolverInterface
     */
    private $variantResolver;

    /**
     * @param EntityManagerInterface $manager
     * @param ProductVariantResolverInterface $variantResolver
     */
    public function __construct(EntityManagerInterface $manager, ProductVariantResolverInterface $variantResolver)
    {
        $this->manager = $manager;
        $this->variantResolver = $variantResolver;
    }

    /**
     * @param GenericEvent $event
     */
    public function lock(GenericEvent $event)
    {
        $subject = $event->getSubject();

        if ($subject instanceof ProductInterface) {
            $this->lockProduct($subject);

            return;
        }

        Assert::isInstanceOf($subject, VersionedInterface::class);

        $this->manager->lock($subject, LockMode::OPTIMISTIC, $subject->getVersion());
    }

    /**
     * @param ProductInterface $product
     */
    private function lockProduct(ProductInterface $product)
    {
        if ($product->isSimple()) {
            /** @var ProductVariantInterface $productVariant */
            $productVariant = $this->variantResolver->getVariant($product);
            $this->manager->lock($productVariant, LockMode::OPTIMISTIC, $productVariant->getVersion());
        }
    }
}
