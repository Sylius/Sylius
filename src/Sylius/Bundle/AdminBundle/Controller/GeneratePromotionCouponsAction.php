<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\AdminBundle\Controller;

use Sylius\Bundle\AdminBundle\Form\Type\PromotionCouponGeneratorInstructionType;
use Sylius\Bundle\CoreBundle\Provider\FlashBagProvider;
use Sylius\Component\Core\Model\PromotionInterface;
use Sylius\Component\Core\Repository\PromotionRepositoryInterface;
use Sylius\Component\Promotion\Generator\PromotionCouponGeneratorInterface;
use Sylius\Resource\Metadata\RegistryInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\RouterInterface;
use Twig\Environment;

/**
 * @experimental
 */
final readonly class GeneratePromotionCouponsAction
{
    /** @param PromotionRepositoryInterface<PromotionInterface> $promotionRepository */
    public function __construct(
        private PromotionRepositoryInterface $promotionRepository,
        private PromotionCouponGeneratorInterface $promotionCouponGenerator,
        private RegistryInterface $resourceRegistry,
        private FormFactoryInterface $formFactory,
        private RequestStack $requestStack,
        private RouterInterface $router,
        private Environment $twig,
    ) {
    }

    public function __invoke(Request $request, string $promotionId): Response
    {
        $promotion = $this->promotionRepository->find($promotionId);
        if (!$promotion instanceof PromotionInterface) {
            throw new NotFoundHttpException('Promotion not found.');
        }

        $form = $this->formFactory->create(PromotionCouponGeneratorInstructionType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->promotionCouponGenerator->generate($promotion, $form->getData());

            FlashBagProvider
                ::getFlashBag($this->requestStack)
                ->add('success', 'sylius.promotion_coupon.generate')
            ;

            return new RedirectResponse($this->router->generate(
                'sylius_admin_promotion_coupon_index',
                ['promotionId' => $promotion->getId()],
            ));
        }

        return new Response(
            $this->twig->render('@SyliusAdmin/promotion_coupon/generate.html.twig', [
                'configuration' => null,
                'form' => $form->createView(),
                'metadata' => $this->resourceRegistry->get('sylius.promotion_coupon'),
                'promotion' => $promotion,
            ]),
        );
    }
}
