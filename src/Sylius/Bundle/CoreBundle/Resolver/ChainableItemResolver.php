<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Resolver;

use Sylius\Component\Cart\Model\CartItemInterface;
use Sylius\Component\Cart\Resolver\ItemResolverInterface;
use Sylius\Component\Cart\Resolver\ItemResolvingException;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Product\Model\VariantInterface;
use Sylius\Component\Registry\ServiceRegistry;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;

class ChainableItemResolver extends ServiceRegistry implements ItemResolverInterface
{
    /**
     * @var RepositoryInterface
     */
    protected $productRepository;

    /**
     * @var FormFactoryInterface
     */
    protected $formFactory;

    /**
     * @var ChannelContextInterface
     */
    protected $channelContext;

    public function __construct(
        RepositoryInterface $productRepository,
        FormFactoryInterface $formFactory,
        ChannelContextInterface $channelContext
    ) {
        parent::__construct('Sylius\Component\Cart\Resolver\ItemResolverInterface');

        $this->productRepository = $productRepository;
        $this->formFactory = $formFactory;
        $this->channelContext = $channelContext;
    }

    /**
     * {@inheritdoc}
     */
    public function resolve(CartItemInterface $item, $data, VariantInterface $variant = null)
    {
        if (!$product = $this->resolveChannel($this->resolveItemIdentifier($data))) {
            throw new ItemResolvingException('Requested product was not found.');
        }

        $variant = $this->resolveProductVariant($item, $product, $data);

        /** @var $resolver ItemResolverInterface */
        foreach ($this->services as $resolver) {
            $resolver->resolve($item, $product, $variant);
        }

        return $item;
    }

    /**
     * Here we resolve the item identifier that is going to be added into the cart.
     *
     * @param mixed|Request $request
     *
     * @return int|string
     *
     * @throws ItemResolvingException
     */
    protected function resolveItemIdentifier($request)
    {
        if (!$request instanceof Request) {
            throw new ItemResolvingException('Invalid request data.');
        }

        if (!$request->isMethod('POST') && !$request->isMethod('PUT')) {
            throw new ItemResolvingException('Invalid request method.');
        }

        // We're getting here product id via query but you can easily override route
        // pattern and use attributes, which are available through request object.
        if (!$id = $request->get('id')) {
            throw new ItemResolvingException('Error while trying to add item to cart.');
        }

        return $id;
    }

    /**
     * @param int|string $id
     *
     * @return ProductInterface
     *
     * @throws ItemResolvingException
     */
    protected function resolveChannel($id)
    {
        $channel = $this->channelContext->getChannel();
        if (!$product = $this->productRepository->findOneBy(array('id' => $id, 'channels' => $channel))) {
            throw new ItemResolvingException('Requested product was not found.');
        }

        return $product;
    }

    /**
     * @param CartItemInterface $item
     * @param ProductInterface  $product
     * @param mixed             $data
     *
     * @return VariantInterface
     *
     * @throws ItemResolvingException
     */
    protected function resolveProductVariant(CartItemInterface $item, ProductInterface $product, $data)
    {
        if (!$item instanceof OrderItemInterface) {
            throw new ItemResolvingException('Requested product can\'t contain variants.');
        }

        // We use forms to easily set the quantity and pick variant but you can do here whatever is required to create the item.
        $form = $this->formFactory->create('sylius_cart_item', $item, array('product' => $product));
        $form->submit($data);

        // If our product has no variants, we simply set the master variant of it.
        if (!$variant = $item->getVariant()) {
            $variant = $product->getMasterVariant();

            $item->setVariant($variant);
        }

        // If all is ok with form, quantity and other stuff, simply return the item.
        if (!$form->isValid() || null === $variant) {
            throw new ItemResolvingException('Submitted form is invalid.');
        }

        return $variant;
    }
}
