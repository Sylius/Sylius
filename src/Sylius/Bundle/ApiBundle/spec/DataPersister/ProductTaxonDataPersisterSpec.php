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

namespace spec\Sylius\Bundle\ApiBundle\DataPersister;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use ApiPlatform\Core\DataPersister\ResumableDataPersisterInterface;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Event\ProductUpdated;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductTaxonInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;

final class ProductTaxonDataPersisterSpec extends ObjectBehavior
{
    function let(
        ContextAwareDataPersisterInterface $decoratedDataPersister,
        MessageBusInterface $eventBus,
    ): void {
        $this->beConstructedWith($decoratedDataPersister, $eventBus);
    }

    function it_is_a_resumable_data_persister(): void
    {
        $this->shouldImplement(ResumableDataPersisterInterface::class);
    }

    function it_supports_only_product_taxon_entity(ProductTaxonInterface $productTaxon, ProductInterface $product): void
    {
        $this->supports($productTaxon)->shouldReturn(true);
        $this->supports($product)->shouldReturn(false);
    }

    function it_uses_decorated_data_persister_to_remove_product_taxon(
        ContextAwareDataPersisterInterface $decoratedDataPersister,
        ProductTaxonInterface $productTaxon,
        ProductInterface $product,
        MessageBusInterface $eventBus,
    ): void {
        $productTaxon->getProduct()->willReturn($product);
        $product->getCode()->willReturn('t_shirt');
        $message = new ProductUpdated('t_shirt');

        $decoratedDataPersister->remove($productTaxon, [])->shouldBeCalled();
        $eventBus->dispatch($message)->willReturn(new Envelope($message))->shouldBeCalled();

        $this->remove($productTaxon, []);
    }

    function it_uses_decorated_data_persister_to_persist_product_taxon_and_product(
        ContextAwareDataPersisterInterface $decoratedDataPersister,
        ProductTaxonInterface $productTaxon,
        ProductInterface $product,
        MessageBusInterface $eventBus,
    ): void {
        $productTaxon->getProduct()->willReturn($product);
        $product->getCode()->willReturn('t_shirt');
        $message = new ProductUpdated('t_shirt');

        $product->addProductTaxon($productTaxon)->shouldBeCalled();
        $decoratedDataPersister->persist($productTaxon, [])->shouldBeCalled();
        $eventBus->dispatch($message)->willReturn(new Envelope($message))->shouldBeCalled();

        $this->persist($productTaxon, []);
    }

    function it_is_resumable(): void
    {
        $this->resumable()->shouldReturn(true);
    }
}
