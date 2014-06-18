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

use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Sylius\Component\Promotion\Generator\CouponGeneratorInterface;
use Sylius\Component\Promotion\Generator\Instruction;
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
        if (null === $promotionId = $request->get('promotionId')) {
            throw new NotFoundHttpException('No promotion id given.');
        }

        $promotion = $this
            ->getPromotionController()
            ->findOr404($request, array('id' => $promotionId))
        ;

        $form = $this->createForm('sylius_promotion_coupon_generate_instruction', new Instruction());

        if ($form->handleRequest($request)->isValid()) {
            $this->getGenerator()->generate($promotion, $form->getData());
            $this->flashHelper->setFlash('success', 'generate');

            return $this->redirectHandler->redirectTo($promotion);
        }

        if ($this->config->isApiRequest()) {
            return $this->handleView($this->view($form));
        }

        $view = $this
            ->view()
            ->setTemplate($this->config->getTemplate('generate.html'))
            ->setData(array(
                'promotion' => $promotion,
                'form'      => $form->createView()
            ))
        ;

        return $this->handleView($view);
    }

    /**
     * {@inheritdoc}
     */
    public function createNew()
    {
        $request = $this->getRequest();
        if (null === $promotionId = $request->get('promotionId')) {
            throw new NotFoundHttpException('No promotion id given');
        }

        $promotion = $this
            ->getPromotionController()
            ->findOr404($request, array('id' => $promotionId))
        ;

        $coupon = parent::createNew();
        $coupon->setPromotion($promotion);

        return $coupon;
    }

    /**
     * Get promotion controller.
     *
     * @return ResourceController
     */
    protected function getPromotionController()
    {
        return $this->get('sylius.controller.promotion');
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
