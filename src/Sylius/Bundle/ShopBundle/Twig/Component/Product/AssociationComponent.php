<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\ShopBundle\Twig\Component\Product;

use Doctrine\Common\Collections\Collection;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Repository\ProductAssociationRepositoryInterface;
use Sylius\Component\Product\Model\ProductAssociationInterface;
use Sylius\TwigHooks\Twig\Component\HookableComponentTrait;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use Symfony\UX\TwigComponent\Attribute\ExposeInTemplate;

#[AsTwigComponent]
class AssociationComponent
{
    use HookableComponentTrait;

    #[ExposeInTemplate('product_association')]
    public ProductAssociationInterface $productAssociation;

    /**
     * @param ProductAssociationRepositoryInterface<ProductAssociationInterface> $productAssociationRepository
     */
    public function __construct(
        protected readonly ProductAssociationRepositoryInterface $productAssociationRepository,
        protected readonly ChannelContextInterface $channelContext,
    ) {
    }

    /**
     * @return Collection<array-key, \Sylius\Component\Product\Model\ProductInterface>
     */
    #[ExposeInTemplate('associated_products')]
    public function associatedProducts(): Collection
    {
        /** @var ChannelInterface $channel */
        $channel = $this->channelContext->getChannel();

        $productAssociation = $this->productAssociationRepository->findWithProductsWithinChannel($this->productAssociation->getId(), $channel);

        return $productAssociation->getAssociatedProducts();
    }
}
