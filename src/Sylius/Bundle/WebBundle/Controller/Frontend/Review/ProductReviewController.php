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
use Sylius\Component\Core\Model\ProductInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @author Justin Hilles <justin@1011i.com>
 * @author Daniel Richter <nexyz9@gmail.com>
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class ProductReviewController extends ResourceController
{
    /**
     * {@inheritdoc}
     */
    public function indexAction(Request $request)
    {
        $product = $this->findProductOr404();

        $criteria = $request->query->get('criteria', array());
        $criteria['reviewSubject'] = $product;
        $request->query->set('criteria', $criteria);

        return parent::indexAction($request);
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function createAction(Request $request)
    {
        $this->isGrantedOr403('create');

        $resource = $this->createNew();
        $form = $this->getForm($resource);

        $view = $this
            ->view()
            ->setTemplate($this->config->getTemplate('create.html'))
            ->setData(array(
                $this->config->getResourceName() => $resource,
                'form'                           => $form->createView(),
            ))
        ;

        return $this->handleView($view);
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function createWithAjaxAction(Request $request)
    {
        $resource = $this->createNew();
        $form = $this->getForm($resource);

        if (!$this->createResourceIfValid($request, $form)) {
            return new JsonResponse($this->getDeepErrors($form));
        }

        return new JsonResponse('success');
    }

    /**
     * {@inheritdoc}
     */
    public function createNew()
    {
        $review = parent::createNew();
        $review->setReviewSubject($this->findProductOr404());

        return $review;
    }

    /**
     * @return ProductInterface
     */
    private function findProductOr404()
    {
        $slug = $this->getRequest()->get('slug');

        if (!$product = $this->get('sylius.repository.product')->findOneBy(array('slug' => $slug))) {
            throw new NotFoundHttpException(sprintf('Product with slug "%s" not found', $slug));
        }

        return $product;
    }

    /**
     * @param Request $request
     * @param FormInterface $form
     *
     * @return bool
     */
    private function createResourceIfValid(Request $request, FormInterface $form)
    {
        if ($form->submit($request)->isValid()) {
            $this->domainManager->create($form->getData());

            return true;
        }

        return false;
    }

    /**
     * @param FormInterface $form
     *
     * @return array
     */
    private function getDeepErrors(FormInterface $form)
    {
        $errors = array();

        foreach ($form as $child) {
            if ($child->isSubmitted() && $child->isValid()) {
                continue;
            }
            $errors[$child->getName()] = $this->getChildErrorsAsArray($child->getErrors());
        }

        return $errors;
    }

    /**
     * @param array $errors
     *
     * @return array
     */
    private function getChildErrorsAsArray(array $errors)
    {
        $childErrors = array();
        foreach ($errors as $error) {
            $childErrors[] = $error->getMessage();
        }

        return $childErrors;
    }
}
