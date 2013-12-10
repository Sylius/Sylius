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

use Sylius\Bundle\CartBundle\Model\CartItemInterface;
use Sylius\Bundle\CartBundle\Resolver\ItemResolverInterface;
use Sylius\Bundle\CartBundle\Resolver\ItemResolvingException;
use Sylius\Bundle\CoreBundle\Model\OrderItem;
use Sylius\Bundle\CoreBundle\Model\Product;
use Sylius\Bundle\InventoryBundle\Checker\AvailabilityCheckerInterface;
use Sylius\Bundle\ResourceBundle\Model\RepositoryInterface;
use Sylius\Bundle\VariableProductBundle\Model\VariantInterface;
use Symfony\Component\Form\FormFactory;
use Symfony\Component\HttpFoundation\Request;
use Sylius\Bundle\CoreBundle\Checker\RestrictedZoneCheckerInterface;

/**
 * Item resolver for cart bundle.
 * Returns proper item objects for cart add and remove actions.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class ItemResolver implements ItemResolverInterface
{
    /**
     * Product manager.
     *
     * @var RepositoryInterface
     */
    private $productRepository;

    /**
     * Form factory.
     *
     * @var FormFactory
     */
    private $formFactory;

    /**
     * Stock availability checker.
     *
     * @var AvailabilityCheckerInterface
     */
    private $availabilityChecker;

    /**
     * Restricted zone checker.
     *
     * @var RestrictedZoneCheckerInterface
     */
    private $restrictedZoneChecker;

    /**
     * Constructor.
     *
     * @param RepositoryInterface            $productRepository
     * @param FormFactory                    $formFactory
     * @param AvailabilityCheckerInterface   $availabilityChecker
     * @param RestrictedZoneCheckerInterface $restrictedZoneChecker
     */
    public function __construct(
        RepositoryInterface            $productRepository,
        FormFactory                    $formFactory,
        AvailabilityCheckerInterface   $availabilityChecker,
        RestrictedZoneCheckerInterface $restrictedZoneChecker
    )
    {
        $this->productRepository = $productRepository;
        $this->formFactory = $formFactory;
        $this->availabilityChecker = $availabilityChecker;
        $this->restrictedZoneChecker = $restrictedZoneChecker;
    }

    /**
     * {@inheritdoc}
     *
     * Here we create the item that is going to be added to cart, basing on the current request.
     */
    public function resolve(CartItemInterface $item, Request $request)
    {
        if (!$request->isMethod('POST')) {
            throw new ItemResolvingException('Wrong request method');
        }

        /*
         * We're getting here product id via query but you can easily override route
         * pattern and use attributes, which are available through request object.
         */
        if (!$id = $request->get('id')) {
            throw new ItemResolvingException('Error while trying to add item to cart');
        }

        /* @var $product Product */
        if (!$product = $this->productRepository->find($id)) {
            throw new ItemResolvingException('Requested product was not found');
        }

        // We use forms to easily set the quantity and pick variant but you can do here whatever is required to create the item.
        $form = $this->formFactory->create('sylius_cart_item', null, array('product' => $product));

        $form->submit($request);
        /* @var $item OrderItem */
        $item = $form->getData();

        // If our product has no variants, we simply set the master variant of it.
        if (!$product->hasVariants()) {
            $item->setVariant($product->getMasterVariant());
        }

        $variant = $item->getVariant();

        // If all is ok with form, quantity and other stuff, simply return the item.
        if (!$form->isValid() || null === $variant) {
            throw new ItemResolvingException('Submitted form is invalid.');
        }

        if (!$this->isStockAvailable($variant)) {
            throw new ItemResolvingException('Selected item is out of stock.');
        }

        if ($this->restrictedZoneChecker->isRestricted($product)) {
            throw new ItemResolvingException('Selected item is not available in your country.');
        }

        $item->setUnitPrice($variant->getPrice());

        return $item;
    }

    /**
     * Check if variant is available in stock.
     *
     * @param VariantInterface $variant
     *
     * @return Boolean
     */
    protected function isStockAvailable(VariantInterface $variant)
    {
        return $this->availabilityChecker->isStockAvailable($variant);
    }
}
