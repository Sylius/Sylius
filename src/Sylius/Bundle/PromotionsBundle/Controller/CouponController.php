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

use Doctrine\Common\Persistence\ObjectRepository;
use Sylius\Bundle\PromotionsBundle\Generator\CouponGeneratorInterface;
use Sylius\Bundle\PromotionsBundle\Generator\Instruction;
use Sylius\Bundle\PromotionsBundle\Model\PromotionInterface;
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
        $promotion = $this->findPromotionOr404($request->get('promotionId'));

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
    protected function createNew(Request $request)
    {
        $promotion = $this->findPromotionOr404($request->get('promotionId'));

        $coupon = parent::createNew($request);
        $coupon->setPromotion($promotion);

        return $coupon;
    }

    /**
     * Get promotion entity.
     *
     * @param integer $id
     *
     * @return PromotionInterface
     *
     * @throws NotFoundHttpException
     */
    protected function findPromotionOr404($id)
    {
        if (!$id) {
            throw new NotFoundHttpException('No promotion id given.');
        }

        if (!$promotion = $this->getPromotionRepository()->find($id)) {
            throw new NotFoundHttpException('Requested promotion does not exist.');
        }

        return $promotion;
    }

    /**
     * Get promotion controller.
     *
     * @return ResourceController
     */
    protected function getPromotionController()
    {
        return $this->get('sylius.controller.product');
    }

    /**
     * Get promotion repository.
     *
     * @return ObjectRepository
     */
    protected function getPromotionRepository()
    {
        return $this->get('sylius.repository.promotion');
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
