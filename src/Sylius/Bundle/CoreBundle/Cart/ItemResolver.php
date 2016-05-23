<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Cart;

use Sylius\Component\Addressing\Checker\RestrictedZoneCheckerInterface;
use Sylius\Component\Cart\Model\CartItemInterface;
use Sylius\Component\Cart\Provider\CartProviderInterface;
use Sylius\Component\Cart\Resolver\ItemResolverInterface;
use Sylius\Component\Cart\Resolver\ItemResolvingException;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Repository\ProductRepositoryInterface;
use Sylius\Component\Inventory\Checker\AvailabilityCheckerInterface;
use Sylius\Component\Pricing\Calculator\DelegatingCalculatorInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class ItemResolver implements ItemResolverInterface
{
    /**
     * @var CartProviderInterface
     */
    protected $cartProvider;

    /**
     * @var DelegatingCalculatorInterface
     */
    protected $priceCalculator;

    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var FormFactoryInterface
     */
    protected $formFactory;

    /**
     * @var AvailabilityCheckerInterface
     */
    protected $availabilityChecker;

    /**
     * @var RestrictedZoneCheckerInterface
     */
    protected $restrictedZoneChecker;

    /**
     * @var ChannelContextInterface
     */
    protected $channelContext;

    /**
     * @param CartProviderInterface          $cartProvider
     * @param RepositoryInterface            $productRepository
     * @param FormFactoryInterface           $formFactory
     * @param AvailabilityCheckerInterface   $availabilityChecker
     * @param RestrictedZoneCheckerInterface $restrictedZoneChecker
     * @param DelegatingCalculatorInterface  $priceCalculator
     * @param ChannelContextInterface        $channelContext
     */
    public function __construct(
        CartProviderInterface          $cartProvider,
        RepositoryInterface            $productRepository,
        FormFactoryInterface           $formFactory,
        AvailabilityCheckerInterface   $availabilityChecker,
        RestrictedZoneCheckerInterface $restrictedZoneChecker,
        DelegatingCalculatorInterface  $priceCalculator,
        ChannelContextInterface        $channelContext
    ) {
        $this->cartProvider = $cartProvider;
        $this->productRepository = $productRepository;
        $this->formFactory = $formFactory;
        $this->availabilityChecker = $availabilityChecker;
        $this->restrictedZoneChecker = $restrictedZoneChecker;
        $this->priceCalculator = $priceCalculator;
        $this->channelContext = $channelContext;
    }

    /**
     * {@inheritdoc}
     */
    public function resolve(CartItemInterface $item, $data)
    {
        $id = $this->resolveItemIdentifier($data);

        $channel = $this->channelContext->getChannel();
        if (!$product = $this->productRepository->findOneByIdAndChannel($id, $channel)) {
            throw new ItemResolvingException('Requested product was not found.');
        }

        if ($this->restrictedZoneChecker->isRestricted($product)) {
            throw new ItemResolvingException('Selected item is not available in your country.');
        }

        // We use forms to easily set the quantity and pick variant but you can do here whatever is required to create the item.
        $form = $this->formFactory->create('sylius_cart_item', $item, ['product' => $product]);
        $form->submit($data);

        // If our product has no variants, we simply set the master variant of it.
        if (null === $item->getVariant() && 1 === $product->getVariants()->count()) {
            $item->setVariant($product->getFirstVariant());
        }

        if (null === $item->getVariant() && 1 > $product->getVariants()->count()) {
            throw new ItemResolvingException('Please select variant');
        }

        $variant = $item->getVariant();

        // If all is ok with form, quantity and other stuff, simply return the item.
        if (!$form->isValid() || null === $variant) {
            throw new ItemResolvingException('Submitted form is invalid.');
        }

        $cart = $this->cartProvider->getCart();
        $quantity = $item->getQuantity();

        $context = ['quantity' => $quantity];

        if (null !== $customer = $cart->getCustomer()) {
            $context['groups'] = $customer->getGroups()->toArray();
        }

        $item->setUnitPrice($this->priceCalculator->calculate($variant, $context));

        foreach ($cart->getItems() as $cartItem) {
            if ($cartItem->equals($item)) {
                $quantity += $cartItem->getQuantity();
                break;
            }
        }

        if (!$this->availabilityChecker->isStockSufficient($variant, $quantity)) {
            throw new ItemResolvingException('Selected item is out of stock.');
        }

        return $item;
    }

    /**
     * @param mixed $request
     *
     * @return string|int
     *
     * @throws ItemResolvingException
     */
    public function resolveItemIdentifier($request)
    {
        if (!$request instanceof Request) {
            throw new ItemResolvingException('Invalid request data.');
        }

        if (!$request->isMethod('POST') && !$request->isMethod('PUT')) {
            throw new ItemResolvingException('Invalid request method.');
        }

        /*
         * We're getting here product id via query but you can easily override route
         * pattern and use attributes, which are available through request object.
         */
        if (!$id = $request->get('id')) {
            throw new ItemResolvingException('Error while trying to add item to cart.');
        }

        return $id;
    }
}
