<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Provider;

use Sylius\Bundle\CoreBundle\Doctrine\ORM\PromotionRepository;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;
use Sylius\Component\Promotion\Provider\PreQualifiedPromotionsProviderInterface;
use Sylius\Component\Resource\Exception\UnexpectedTypeException;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class ActiveByChannelPromotionsProvider implements PreQualifiedPromotionsProviderInterface
{
    /**
     * @var PromotionRepository
     */
    private $promotionRepository;

    /**
     * @param PromotionRepository $promotionRepository
     */
    public function __construct(PromotionRepository $promotionRepository)
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
        $promotions = $this->promotionRepository->findActiveByChannel($channel);

        return $promotions;
    }
}
