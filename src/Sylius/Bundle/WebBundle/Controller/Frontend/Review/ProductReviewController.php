<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\WebBundle\Controller\Frontend\Review;

use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * ProductReview controller.
 *
 * @author Justin Hilles <justin@1011i.com>
 * @author Daniel Richter <nexyz9@gmail.com>
 */
class ProductReviewController extends ResourceController
{
    /**
     * Overrides parent to filter reviews by related product
     *
     * {@inheritdoc}
     */
    public function indexAction(Request $request)
    {
        $product = $this->findProductOr404();

        $criteria = $request->query->get('criteria', array());
        $criteria['product'] = $product;
        $request->query->set('criteria', $criteria);

        return parent::indexAction($request);
    }

    /**
     * Overrides parent to customize form
     */
    public function getForm($resource = null)
    {
        $product = $this->findProductOr404();

        if ($resource) {
            $resource->setProduct($product);

            if ($user = $this->getUser()) {
                $resource->setUser($user);
            }
        }

        return $this->createForm($this->getConfiguration()->getFormType(), $resource, array(
            'action' => $this->generateUrl('sylius_review_product', array('slug' => $product->getSlug())),
            'allow_guest' => true
        ));
    }

    public function redirectTo($resource)
    {
        $product = $resource->getProduct();

        return $this->redirect($this->generateUrl('sylius_product_show', array('slug' => $product->getSlug())));
    }

    protected function findProductOr404()
    {
        $slug = $this->getRequest()->get('slug');

        if (!$product = $this->get('sylius.repository.product')->findOneBy(array('slug' => $slug))) {
            throw new NotFoundHttpException(sprintf('Product with slug "%s" not found', $slug));
        }

        return $product;
    }
}
