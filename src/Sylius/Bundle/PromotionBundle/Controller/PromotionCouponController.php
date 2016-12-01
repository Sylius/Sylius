<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\PromotionBundle\Controller;

use FOS\RestBundle\View\View;
use Sylius\Bundle\PromotionBundle\Form\Type\PromotionCouponGeneratorInstructionType;
use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Sylius\Component\Promotion\Generator\PromotionCouponGeneratorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class PromotionCouponController extends ResourceController
{
    /**
     * @param Request $request
     *
     * @return Response
     *
     * @throws NotFoundHttpException
     */
    public function generateAction(Request $request)
    {
        $configuration = $this->requestConfigurationFactory->create($this->metadata, $request);

        if (null === $promotionId = $request->attributes->get('promotionId')) {
            throw new NotFoundHttpException('No promotion id given.');
        }

        if (null === $promotion = $this->container->get('sylius.repository.promotion')->find($promotionId)) {
            throw new NotFoundHttpException('Promotion not found.');
        }

        $form = $this->container->get('form.factory')->create(PromotionCouponGeneratorInstructionType::class);

        if ($form->handleRequest($request)->isValid()) {
            $this->getGenerator()->generate($promotion, $form->getData());
            $this->flashHelper->addSuccessFlash($configuration, 'generate');

            return $this->redirectHandler->redirectToResource($configuration, $promotion);
        }

        if (!$configuration->isHtmlRequest()) {
            return $this->viewHandler->handle($configuration, View::create($form));
        }

        $view = View::create()
            ->setTemplate($configuration->getTemplate('generate.html'))
            ->setData([
                'configuration' => $configuration,
                'metadata' => $this->metadata,
                'promotion' => $promotion,
                'form' => $form->createView(),
            ])
        ;

        return $this->viewHandler->handle($configuration, $view);
    }

    /**
     * @return PromotionCouponGeneratorInterface
     */
    protected function getGenerator()
    {
        return $this->container->get('sylius.promotion_coupon_generator');
    }
}
