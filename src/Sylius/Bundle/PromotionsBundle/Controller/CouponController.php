<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\PromotionsBundle\Controller;

use Sylius\Bundle\PromotionsBundle\Generator\CouponGeneratorInterface;
use Sylius\Bundle\PromotionsBundle\Generator\Instruction;
use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
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
            throw new NotFoundHttpException('No promotion id given');
        }

        $promotion = $this
            ->getPromotionController()
            ->findOr404(array('id' => $promotionId))
        ;

        $form = $this->createForm('sylius_promotion_coupon_generate_instruction', new Instruction());

        if ($request->isMethod('POST') && $form->submit($request)->isValid()) {
            $this->getGenerator()->generate($promotion, $form->getData());
            $this->setFlash('success', 'generate');

            return $this
                ->getPromotionController()
                ->redirectTo($promotion)
            ;
        }

        $config = $this->getConfiguration();
        if ($config->isApiRequest()) {
            return $this->handleView($this->view($form));
        }

        $view = $this
            ->view()
            ->setTemplate($config->getTemplate('generate.html'))
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
        if (null === $promotionId = $this->getRequest()->get('promotionId')) {
            throw new NotFoundHttpException('No promotion id given');
        }

        $promotion = $this
            ->getPromotionController()
            ->findOr404(array('id' => $promotionId))
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
