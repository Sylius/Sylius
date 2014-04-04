<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ProductBundle\Controller;

use Doctrine\Common\Persistence\ObjectRepository;
use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Sylius\Component\Product\Generator\VariantGeneratorInterface;
use Sylius\Component\Product\Model\ProductInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Variant controller.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@sylius.pl>
 */
class VariantController extends ResourceController
{
    /**
     * Generate all possible variants for given product id.
     */
    public function generateAction(Request $request)
    {
        if (null === $productId = $request->get('productId')) {
            throw new NotFoundHttpException('No product given.');
        }

        $product = $this->findProductOr404($productId);
        $this->getGenerator()->generate($product);

        $manager = $this->get('sylius.manager.product');
        $manager->persist($product);
        $manager->flush();

        $this->flashHelper->setFlash('success', 'generate');

        return $this->redirectHandler->redirectTo($product);
    }

    /**
     * {@inheritdoc}
     */
    public function createNew()
    {
        if (null === $productId = $this->getRequest()->get('productId')) {
            throw new NotFoundHttpException('No parent product given.');
        }

        $product = $this->findProductOr404($productId);

        $variant = parent::createNew();
        $variant->setProduct($product);

        return $variant;
    }

    /**
     * Get variant generator.
     *
     * @return VariantGeneratorInterface
     */
    protected function getGenerator()
    {
        return $this->get('sylius.generator.product_variant');
    }

    /**
     * Get product repository.
     *
     * @return ObjectRepository
     */
    protected function getProductRepository()
    {
        return $this->get('sylius.repository.product');
    }

    /**
     * Get product or 404.
     *
     * @param integer $id
     *
     * @return ProductInterface
     *
     * @throws NotFoundHttpException
     */
    protected function findProductOr404($id)
    {
        if (!$product = $this->getProductRepository()->find($id)) {
            throw new NotFoundHttpException('Requested product does not exist.');
        }

        return $product;
    }
}
