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

use Doctrine\Common\Persistence\ObjectManager;
use FOS\RestBundle\Controller\FOSRestController;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Review\Model\ReviewInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @author Justin Hilles <justin@1011i.com>
 * @author Daniel Richter <nexyz9@gmail.com>
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
class ProductReviewController extends FOSRestController
{
    /**
     * @param Request $request
     *
     * @return Response
     */
    public function indexAction(Request $request)
    {
        $product = $this->findProductOr404($request->attributes->get('slug'));
        $reviews = $this->getProductReviewRepository()->findBy([
            'reviewSubject' => $product,
            'status' => ReviewInterface::STATUS_ACCEPTED,
        ]);

        $view = $this
            ->view()
            ->setTemplate('SyliusWebBundle:Frontend/Review:_list.html.twig')
            ->setData([
                'product_reviews' => $reviews,
            ])
        ;

        return $this->handleView($view);
    }

    /**
     * @return Response
     */
    public function createAction()
    {
        $review = $this->getProductReviewFactory()->createNew();
        $form = $this->getReviewForm($review);

        $view = $this
            ->view()
            ->setTemplate('SyliusWebBundle:Frontend/Review:_form.html.twig')
            ->setData([
                'product_review' => $review,
                'form' => $form->createView(),
            ])
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
        $review = $this->getProductReviewFactory()->createNew();
        $form = $this->getReviewForm($review);

        if ($form->submit($request)->isValid()) {
            $manager = $this->getProductReviewManager();
            $review = $form->getData();
            $product = $this->findProductOr404($request->attributes->get('slug'));
            $review->setReviewSubject($product);

            $manager->persist($review);
            $manager->flush();

            $this->addFlashMessage('success', 'sylius.resource.create', ['%resource%' => 'Product review']);

            return new JsonResponse('success');
        }

        return new JsonResponse($this->getFormErrorsAsArray($form));
    }

    /**
     * @param string $slug
     *
     * @return ProductInterface
     *
     * @throws NotFoundHttpException
     */
    private function findProductOr404($slug)
    {
        if (!$product = $this->getProductRepository()->findOneBy(['slug' => $slug])) {
            throw new NotFoundHttpException(sprintf('Product with slug "%s" not found', $slug));
        }

        return $product;
    }

    /**
     * @param FormInterface $form
     *
     * @return array
     */
    private function getFormErrorsAsArray(FormInterface $form)
    {
        $errors = [];
        foreach ($form->getErrors(true) as $error) {
            $errors[$error->getCause()->getPropertyPath()] = $error->getMessage();
        }

        return $errors;
    }

    /**
     * @return FactoryInterface
     */
    private function getProductReviewFactory()
    {
        return $this->get('sylius.factory.product_review');
    }

    /**
     * @return EntityRepository
     */
    private function getProductReviewRepository()
    {
        return $this->get('sylius.repository.product_review');
    }

    /**
     * @return EntityRepository
     */
    private function getProductRepository()
    {
        return $this->get('sylius.repository.product');
    }

    /**
     * @param ReviewInterface $review
     *
     * @return FormInterface
     */
    private function getReviewForm(ReviewInterface $review)
    {
        return $this->get('form.factory')->create('sylius_product_review', $review);
    }

    /**
     * @return ObjectManager
     */
    private function getProductReviewManager()
    {
        return $this->get('sylius.manager.product_review');
    }

    /**
     * @param string $type
     * @param string $message
     * @param array $parameters
     *
     * @throws \LogicException
     */
    private function addFlashMessage($type, $message, array $parameters)
    {
        if (!$this->container->has('session')) {
            throw new \LogicException('You can not use the addFlash method if sessions are disabled.');
        }

        $translator = $this->container->get('translator');
        $this->container->get('session')->getFlashBag()->add($type, $translator->trans($message, $parameters, 'flashes'));
    }
}
