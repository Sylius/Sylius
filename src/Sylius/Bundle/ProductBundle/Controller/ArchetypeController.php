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
use Sylius\Component\Archetype\Builder\ArchetypeBuilderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Archetype controller.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@sylius.pl>
 */
class ArchetypeController extends ResourceController
{
    /**
     * Build the object from the given archetype.
     *
     * @param Request $request
     * @param mixed   $id
     *
     * @return Response
     */
    public function buildAction(Request $request, $id)
    {
        $archetype = $this->findOr404($request, array('id' => $id));
        $productController = $this->getProductController();

        $product = $productController->createNew();

        $this
            ->getBuilder()
            ->build($archetype, $product)
        ;

        $form = $productController->getForm($product);

        if ($form->handleRequest($request)->isValid()) {
            $manager = $this->get('doctrine')->getManager();
            $manager->persist($product);
            $manager->flush();

            $this->flashHelper->setFlash('success', 'Product has been successfully created.');

            return $this->redirectHandler->redirectTo($product);
        }

        return $productController->render($this->config->getTemplate('build.html'), array(
            'product_archetype' => $archetype,
            'product'           => $product,
            'form'              => $form->createView()
        ));
    }

    /**
     * Get product controller.
     *
     * @return ResourceController
     */
    protected function getProductController()
    {
        return $this->get('sylius.controller.product');
    }

    /**
     * Get archetype builder.
     *
     * @return ArchetypeBuilderInterface
     */
    protected function getBuilder()
    {
        return $this->get('sylius.builder.product_archetype');
    }
}
