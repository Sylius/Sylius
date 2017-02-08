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

use Doctrine\DBAL\LockMode;
use Doctrine\ORM\OptimisticLockException;
use FOS\RestBundle\View\View;
use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Sylius\Component\Product\Resolver\ProductVariantResolverInterface;
use Sylius\Component\Resource\ResourceActions;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
class ProductController extends ResourceController
{
    /**
     * {@inheritdoc}
     */
    public function updateAction(Request $request)
    {
        $configuration = $this->requestConfigurationFactory->create($this->metadata, $request);

        $this->isGrantedOr403($configuration, ResourceActions::UPDATE);
        $product = $this->findOr404($configuration);

        $form = $this->resourceFormFactory->create($configuration, $product);

        if (in_array($request->getMethod(), ['POST', 'PUT', 'PATCH'], true) && $form->handleRequest($request)->isValid()) {
            $product = $form->getData();

            $event = $this->eventDispatcher->dispatchPreEvent(ResourceActions::UPDATE, $configuration, $product);

            if ($event->isStopped() && !$configuration->isHtmlRequest()) {
                throw new HttpException($event->getErrorCode(), $event->getMessage());
            }
            if ($event->isStopped()) {
                $this->flashHelper->addFlashFromEvent($configuration, $event);

                return $this->redirectHandler->redirectToResource($configuration, $product);
            }

            try {
                $this->manager->flush();
            } catch (OptimisticLockException $exception) {
                $this->addFlash('error', 'sylius.product.update_error');

                return $this->redirectHandler->redirectToResource($configuration, $product);
            }

            $this->eventDispatcher->dispatchPostEvent(ResourceActions::UPDATE, $configuration, $product);

            if (!$configuration->isHtmlRequest()) {
                return $this->viewHandler->handle($configuration, View::create(null, Response::HTTP_NO_CONTENT));
            }

            $this->flashHelper->addSuccessFlash($configuration, ResourceActions::UPDATE, $product);

            return $this->redirectHandler->redirectToResource($configuration, $product);
        }

        if ($product->isSimple()) {
            $productVariant = $this->getProductVariantResolver()->getVariant($product);
            $this->manager->lock($productVariant, LockMode::OPTIMISTIC, $productVariant->getVersion());
        }

        if (!$configuration->isHtmlRequest()) {
            return $this->viewHandler->handle($configuration, View::create($form, Response::HTTP_BAD_REQUEST));
        }

        $view = View::create()
            ->setData([
                'configuration' => $configuration,
                'metadata' => $this->metadata,
                'resource' => $product,
                $this->metadata->getName() => $product,
                'form' => $form->createView(),
            ])
            ->setTemplate($configuration->getTemplate(ResourceActions::UPDATE . '.html'))
        ;

        return $this->viewHandler->handle($configuration, $view);
    }

    /**
     * @return ProductVariantResolverInterface
     */
    protected function getProductVariantResolver()
    {
        return $this->container->get('sylius.product_variant_resolver.default');
    }
}
