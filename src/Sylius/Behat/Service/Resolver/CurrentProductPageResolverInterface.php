<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Service\Resolver;

use Sylius\Behat\Page\SymfonyPageInterface;
use Sylius\Component\Product\Model\ProductInterface;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
interface CurrentProductPageResolverInterface extends CurrentPageResolverInterface
{
    /**
     * @param SymfonyPageInterface[] $pages
     * @param ProductInterface|null $product
     *
     * @return SymfonyPageInterface
     */
    public function getCurrentPageWithForm(array $pages, ProductInterface $product = null);
}
