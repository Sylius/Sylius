<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Page\Admin\ProductVariant;

use Behat\Mink\Exception\ElementNotFoundException;
use Sylius\Behat\Page\Admin\Crud\IndexPage as BaseIndexPage;
use Sylius\Component\Core\Model\ProductVariantInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
final class IndexPage extends BaseIndexPage implements IndexPageInterface
{
    /**
     * {@inheritdoc}
     */
    public function hasOnHandQuantity(ProductVariantInterface $productVariant, $quantity)
    {
        try {
            $onHandQuantity = (int) $this->getElement('onHandQuantity', ['%id%' => $productVariant->getId()])->getText();

            return $quantity === $onHandQuantity;
        } catch (ElementNotFoundException $exception) {
            return false;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function hasOnHoldQuantity(ProductVariantInterface $productVariant, $quantity)
    {
        try {
            $onHoldQuantity = (int) $this->getElement('onHoldQuantity', ['%id%' => $productVariant->getId()])->getText();

            return $quantity === $onHoldQuantity;
        } catch (ElementNotFoundException $exception) {
            return false;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getDefinedElements()
    {
        return array_merge(parent::getDefinedElements(), [
            'onHandQuantity' => '#sylius-%id%-on-hand-quantity',
            'onHoldQuantity' => '#sylius-%id%-on-hold-quantity',
        ]);
    }
}
