<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Core\Provider;

use Sylius\Core\Model\OrderInterface;
use Sylius\Core\Repository\PromotionRepositoryInterface;
use Sylius\Promotion\Model\PromotionSubjectInterface;
use Sylius\Promotion\Provider\PreQualifiedPromotionsProviderInterface;
use Sylius\Resource\Exception\UnexpectedTypeException;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class ActivePromotionsByChannelProvider implements PreQualifiedPromotionsProviderInterface
{
    /**
     * @var PromotionRepositoryInterface
     */
    private $promotionRepository;

    /**
     * @param PromotionRepositoryInterface $promotionRepository
     */
    public function __construct(PromotionRepositoryInterface $promotionRepository)
    {
        $this->promotionRepository = $promotionRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function getPromotions(PromotionSubjectInterface $subject)
    {
        if (!$subject instanceof OrderInterface) {
            throw new UnexpectedTypeException($subject, OrderInterface::class);
        }

        $channel = $subject->getChannel();
        if (null === $channel) {
            throw new \InvalidArgumentException('Order has no channel, but it should.');
        }

        $promotions = $this->promotionRepository->findActiveByChannel($channel);

        return $promotions;
    }
}
