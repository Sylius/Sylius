<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\OrderProcessing;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Order\Model\OrderInterface as BaseOrderInterface;
use Sylius\Component\Order\Processor\OrderProcessorInterface;
use Sylius\Component\Promotion\Processor\PromotionProcessorInterface;
use Webmozart\Assert\Assert;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class OrderPromotionProcessor implements OrderProcessorInterface
{
    /**
     * @var PromotionProcessorInterface
     */
    private $promotionProcessor;

    /**
     * @param PromotionProcessorInterface $promotionProcessor
     */
    public function __construct(PromotionProcessorInterface $promotionProcessor)
    {
        $this->promotionProcessor = $promotionProcessor;
    }

    /**
     * {@inheritdoc}
     */
    public function process(BaseOrderInterface $order)
    {
        /** @var OrderInterface $order */
        Assert::isInstanceOf($order, OrderInterface::class);

        $this->promotionProcessor->process($order);
    }
}
