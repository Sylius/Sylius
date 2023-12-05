<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Twig;

use Sylius\Component\Order\Model\AdjustmentInterface;
use Sylius\Component\Promotion\Model\PromotionInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Routing\RouterInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class GenerateUrlFromAdjustmentExtension extends AbstractExtension
{
    public function __construct(
        private RouterInterface $router,
        private RepositoryInterface $adjustmentRepository,
        private RepositoryInterface $promotionRepository,
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('sylius_generate_url_from_adjustment', [$this, 'generateUrlFromAdjustment']),
        ];
    }

    public function generateUrlFromAdjustment(string $label): string
    {
        /** @var AdjustmentInterface $adjustment */
        $adjustment = $this->adjustmentRepository->findOneBy(['label' => $label]);
        /** @var PromotionInterface $promotion */
        $promotion = $this->promotionRepository->findOneBy(['code' => $adjustment->getOriginCode()]);

        return $this->router->generate('sylius_admin_promotion_update', ['id' => $promotion->getId()]);
    }
}
