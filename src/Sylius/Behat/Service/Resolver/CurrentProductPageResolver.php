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

use Sylius\Behat\Page\Admin\Crud\UpdatePageInterface;
use Sylius\Behat\Page\Admin\Product\UpdateConfigurableProductPageInterface;
use Sylius\Behat\Page\Admin\Product\UpdateSimpleProductPageInterface;
use Sylius\Component\Product\Model\ProductInterface;
use Webmozart\Assert\Assert;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
final class CurrentProductPageResolver implements CurrentProductPageResolverInterface
{
    /**
     * @var CurrentPageResolverInterface
     */
    private $currentPageResolver;

    /**
     * @param CurrentPageResolverInterface $currentPageResolver
     */
    public function __construct(CurrentPageResolverInterface $currentPageResolver)
    {
        $this->currentPageResolver = $currentPageResolver;
    }

    /**
     * {@inheritdoc}
     * 
     * @throws \LogicException
     */
    public function getCurrentPageWithForm(array $pages, ProductInterface $product = null)
    {
        $resolvedPage = $this->currentPageResolver->getCurrentPageWithForm($pages);

        if (!$resolvedPage instanceof UpdatePageInterface) {
            return $resolvedPage;
        }

        Assert::notNull($product, 'It is not possible to determine a product edit page without product provided.');

        if ($product->isSimple()) {
            $resolvedPage = $this->getSimplePage($pages);
        } else {
            $resolvedPage = $this->getConfigurablePage($pages);
        }

        Assert::notNull($resolvedPage, 'Route name could not be matched to provided pages.');

        return $resolvedPage;
    }

    /**
     * @param array $pages
     *
     * @return UpdateSimpleProductPageInterface|null
     */
    private function getSimplePage(array $pages)
    {
        foreach ($pages as $page) {
            if ($page instanceof UpdateSimpleProductPageInterface) {
                return $page;
            }
        }

        return null;
    }

    /**
     * @param array $pages
     *
     * @return UpdateConfigurableProductPageInterface|null
     */
    private function getConfigurablePage(array $pages)
    {
        foreach ($pages as $page) {
            if ($page instanceof UpdateConfigurableProductPageInterface) {
                return $page;
            }
        }

        return null;
    }
}
