<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\ApiBundle\DataPersister;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use Sylius\Component\Core\Event\ProductUpdated;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductTaxonInterface;
use Symfony\Component\Messenger\MessageBusInterface;

/** @experimental */
final class ProductTaxonDataPersister implements ContextAwareDataPersisterInterface
{
    public function __construct(
        private ContextAwareDataPersisterInterface $decoratedDataPersister,
        private MessageBusInterface $eventBus
    ){
    }

    public function supports($data, array $context = []): bool
    {
        return $data instanceof ProductTaxonInterface;
    }

    public function persist($data, array $context = [])
    {
        /** @var ProductInterface $product */
        $product = $data->getProduct();
        $product->addProductTaxon($data);

        $this->decoratedDataPersister->persist($data, $context);

        $this->eventBus->dispatch(new ProductUpdated($product->getCode()));
    }

    public function remove($data, array $context = [])
    {
        return $this->decoratedDataPersister->remove($data, $context);
    }
}
