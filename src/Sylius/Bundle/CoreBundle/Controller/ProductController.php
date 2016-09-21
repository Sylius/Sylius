<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Controller;

use FOS\RestBundle\View\View;
use Sylius\Bundle\ResourceBundle\Controller\RequestConfiguration;
use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Resource\ResourceActions;
use Symfony\Component\HttpFoundation\Request;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class ProductController extends ResourceController
{
    /**
     * {@inheritdoc}
     */
    public function showAction(Request $request)
    {
        $configuration = $this->requestConfigurationFactory->create($this->metadata, $request);

        $this->isGrantedOr403($configuration, ResourceActions::SHOW);
        /** @var ProductInterface $resource */
        $resource = $this->findOr404($configuration);

        $this->eventDispatcher->dispatch(ResourceActions::SHOW, $configuration, $resource);

        $view = View::create($resource);

        if ($configuration->isHtmlRequest()) {
            $view = $this->prepareHtmlRequestView($resource, $configuration, $view);
        }

        return $this->viewHandler->handle($configuration, $view);
    }

    /**
     * @param ProductInterface $product
     * @param RequestConfiguration $configuration
     * @param View $view
     *
     * @return View
     */
    protected function prepareHtmlRequestView(ProductInterface $product, RequestConfiguration $configuration, View $view)
    {
        $templateData = [
            'configuration' => $configuration,
            'metadata' => $this->metadata,
            'resource' => $product,
            $this->metadata->getName() => $product,
        ];

        if (
            $product->isConfigurable() &&
            ProductInterface::VARIANT_SELECTION_MATCH === $product->getVariantSelectionMethod()
        ) {
            $templateData['variantsPrices'] = $this->getVariantsPrices($product);
        }

        return $view
            ->setTemplate($configuration->getTemplate(ResourceActions::SHOW . '.html'))
            ->setTemplateVar($this->metadata->getName())
            ->setData($templateData)
        ;
    }

    /**
     * @param ProductInterface $product
     *
     * @return array
     */
    protected function getVariantsPrices(ProductInterface $product)
    {
        return $this
            ->get('sylius.provider.product_variants_prices')
            ->provideVariantsPrices($product)
        ;
    }
}
