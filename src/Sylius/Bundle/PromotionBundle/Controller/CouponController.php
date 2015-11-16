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
use Sylius\Bundle\ResourceBundle\Controller\RequestConfiguration;
use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Sylius\Component\Promotion\Generator\CouponGeneratorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Coupon controller.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@sylius.pl>
 */
class CouponController extends ResourceController
{
    /**
     * @param Request $request
     *
     * @return Response
     * @throws NotFoundHttpException
     */
    public function generateAction(Request $request)
    {
        $configuration = $this->configurationFactory->create($this->metadata, $request);

        if (null === $promotionId = $request->get('promotionId')) {
            throw new NotFoundHttpException('No promotion id given.');
        }

        $promotionRepository = $this->get('sylius.repository.promotion');

        if (!$promotion = $promotionRepository->find($promotionId)) {
            throw new NotFoundHttpException('Requested promotion does not exist.');
        }

        $form = $this->createForm('sylius_promotion_coupon_generate_instruction');

        if ($form->handleRequest($request)->isValid()) {
            $this->getGenerator()->generate($promotion, $form->getData());
            $this->get('session')->getBag('flashes')->add('success', $this->get('translator')->trans('sylius.promotion_coupon.generate', array(), 'flashes'));

            return $this->redirectHandler->redirectToResource($configuration, $promotion);
        }

        if (!$configuration->isHtmlRequest()) {
            return $this->handleView($configuration, View::create($form));
        }

        $view = View::create()
            ->setTemplate($configuration->getTemplate('generate.html'))
            ->setData(array(
                'promotion' => $promotion,
                'form'      => $form->createView()
            ))
        ;

        return $this->handleView($configuration, $view);
    }

    /**
     * {@inheritdoc}
     */
    public function createNew(RequestConfiguration $configuration)
    {
        $request = $configuration->getRequest();

        if (null === $promotionId = $request->get('promotionId')) {
            throw new NotFoundHttpException('No promotion id given');
        }

        $promotionRepository = $this->get('sylius.repository.promotion');

        if (!$promotion = $promotionRepository->find($promotionId)) {
            throw new NotFoundHttpException('Requested promotion does not exist.');
        }

        $coupon = parent::createNew($configuration);
        $coupon->setPromotion($promotion);

        return $coupon;
    }

    /**
     * Get coupon code generator.
     *
     * @return CouponGeneratorInterface
     */
    protected function getGenerator()
    {
        return $this->get('sylius.generator.promotion_coupon');
    }
}
