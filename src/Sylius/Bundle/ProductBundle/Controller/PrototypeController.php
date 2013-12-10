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

use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Tests\Controller;
use Sylius\Bundle\ProductBundle\Builder\PrototypeBuilderInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * Prototype controller.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@sylius.pl>
 */
class PrototypeController extends ResourceController
{
    /**
     * Build a product from the given prototype.
     * Everything else works exactly like in product
     * creation action.
     *
     * @param Request $request
     * @param mixed   $id
     *
     * @return Response
     */
    public function buildAction(Request $request, $id)
    {
        $prototype = $this->findOr404(array('id' => $id));
        $productController = $this->getProductController();

        $product = $productController->createNew();

        $this
            ->getBuilder()
            ->build($prototype, $product)
        ;

        $form = $productController->getForm($product);

        if ($request->isMethod('POST') && $form->submit($request)->isValid()) {
            $manager = $productController->getManager();

            $manager->persist($product);
            $manager->flush();

            $productController->setFlash('success', '%resource% has been successfully created.');

            return $productController->redirectTo($product);
        }

        return $productController->renderResponse('build.html', array(
            'prototype' => $prototype,
            'product'   => $product,
            'form'      => $form->createView()
        ));
    }

    /**
     * Get product controller.
     *
     * @return Controller
     */
    protected function getProductController()
    {
        return $this->get('sylius.controller.product');
    }

    /**
     * Get prototype builder.
     *
     * @return PrototypeBuilderInterface
     */
    protected function getBuilder()
    {
        return $this->get('sylius.builder.prototype');
    }
}
