<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Page\Shop\Product;

use Sylius\Behat\Page\SymfonyPage;
use Sylius\Component\Core\Model\ProductInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
class ShowPage extends SymfonyPage implements ShowPageInterface
{
    /**
     * {@inheritdoc}
     */
    public function addToCart()
    {
        $this->getDocument()->pressButton('Add to cart');
    }

    /**
     * {@inheritdoc}
     */
    public function addToCartWithQuantity($quantity)
    {
        $this->getDocument()->fillField('Quantity', $quantity);
        $this->getDocument()->pressButton('Add to cart');
    }

    /**
     * {@inheritdoc}
     */
    public function addToCartWithVariant($variant)
    {
        $item = $this->getDocument()->find('css', sprintf('#product-variants tbody tr:contains("%s")', $variant));
        $radio = $item->find('css', 'input');

        $this->getDocument()->fillField($radio->getAttribute('name'), $radio->getAttribute('value'));

        $this->getDocument()->pressButton('Add to cart');
    }

    /**
     * {@inheritdoc}
     */
    public function getRouteName()
    {
        // Intentionally left blank, overriding getUrl method not to use it
    }

    /**
     * {@inheritdoc}
     */
    protected function getUrl(array $urlParameters = [])
    {
        if (!isset($urlParameters['product']) || !$urlParameters['product'] instanceof ProductInterface) {
            throw new \InvalidArgumentException(
                'There should be only one url parameter passed to ProductShowPage '.
                'named "product", containing an instance of Core\'s ProductInterface'
            );
        }

        $path = $this->router->generate($urlParameters['product']);

        return $this->makePathAbsolute($path);
    }
}
