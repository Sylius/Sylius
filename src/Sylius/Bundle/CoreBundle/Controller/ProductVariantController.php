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
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Resource\ResourceActions;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
class ProductVariantController extends ResourceController
{
    /**
     * {@inheritdoc}
     */
    public function updateAction(Request $request)
    {
        $configuration = $this->requestConfigurationFactory->create($this->metadata, $request);

        $this->isGrantedOr403($configuration, ResourceActions::UPDATE);
        $productVariant = $this->findOr404($configuration);

        $form = $this->resourceFormFactory->create($configuration, $productVariant);

        if (in_array($request->getMethod(), ['POST', 'PUT', 'PATCH'], true) && $form->handleRequest($request)->isValid()) {
            $productVariant = $form->getData();

            $event = $this->eventDispatcher->dispatchPreEvent(ResourceActions::UPDATE, $configuration, $productVariant);

            if ($event->isStopped() && !$configuration->isHtmlRequest()) {
                throw new HttpException($event->getErrorCode(), $event->getMessage());
            }
            if ($event->isStopped()) {
                $this->flashHelper->addFlashFromEvent($configuration, $event);

                return $this->redirectHandler->redirectToResource($configuration, $productVariant);
            }

            try {
                $this->manager->flush();
            } catch (OptimisticLockException $exception) {
                $this->addFlash('error', 'sylius.product_variant.update_error');

                return $this->redirectHandler->redirectToResource($configuration, $productVariant);
            }

            $this->eventDispatcher->dispatchPostEvent(ResourceActions::UPDATE, $configuration, $productVariant);

            if (!$configuration->isHtmlRequest()) {
                return $this->viewHandler->handle($configuration, View::create(null, Response::HTTP_NO_CONTENT));
            }

            $this->flashHelper->addSuccessFlash($configuration, ResourceActions::UPDATE, $productVariant);

            return $this->redirectHandler->redirectToResource($configuration, $productVariant);
        }

        $this->manager->lock($productVariant, LockMode::OPTIMISTIC, $productVariant->getVersion());

        if (!$configuration->isHtmlRequest()) {
            return $this->viewHandler->handle($configuration, View::create($form, Response::HTTP_BAD_REQUEST));
        }

        $view = View::create()
            ->setData([
                'configuration' => $configuration,
                'metadata' => $this->metadata,
                'resource' => $productVariant,
                $this->metadata->getName() => $productVariant,
                'form' => $form->createView(),
            ])
            ->setTemplate($configuration->getTemplate(ResourceActions::UPDATE . '.html'))
        ;

        return $this->viewHandler->handle($configuration, $view);
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     *
     * @throws HttpException
     */
    public function updatePositionsAction(Request $request)
    {
        $configuration = $this->requestConfigurationFactory->create($this->metadata, $request);
        $this->isGrantedOr403($configuration, ResourceActions::UPDATE);
        $productVariantsToUpdate = $request->get('productVariants');

        if ($configuration->isCsrfProtectionEnabled() && !$this->isCsrfTokenValid('update-product-variant-position', $request->request->get('_csrf_token'))) {
            throw new HttpException(Response::HTTP_FORBIDDEN, 'Invalid csrf token.');
        }

        if (in_array($request->getMethod(), ['POST', 'PUT', 'PATCH'], true) && null !== $productVariantsToUpdate) {
            foreach ($productVariantsToUpdate as $productVariantToUpdate) {
                if (!is_numeric($productVariantToUpdate['position'])) {
                    throw new HttpException(
                        Response::HTTP_NOT_ACCEPTABLE,
                        sprintf('The product variant position "%s" is invalid.', $productVariantToUpdate['position'])
                    );
                }

                /** @var ProductVariantInterface $productVariant */
                $productVariant = $this->repository->findOneBy(['id' => $productVariantToUpdate['id']]);
                $productVariant->setPosition($productVariantToUpdate['position']);
                $this->manager->flush();
            }
        }

        return new JsonResponse();
    }
}
