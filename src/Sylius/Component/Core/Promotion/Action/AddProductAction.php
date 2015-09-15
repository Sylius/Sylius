<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\Promotion\Action;

use Sylius\Component\Order\Model\OrderInterface;
use Sylius\Component\Order\Model\OrderItemInterface;
use Sylius\Component\Originator\Originator\OriginatorInterface;
use Sylius\Component\Promotion\Action\PromotionActionInterface;
use Sylius\Component\Promotion\Model\PromotionInterface;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;
use Sylius\Component\Resource\Exception\UnexpectedTypeException;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * Free product action.
 *
 * @author Alexandre Bacco <alexandre.bacco@gmail.com>
 * @author Joseph Bielawski <stloyd@gmail.com>
 */
class AddProductAction implements PromotionActionInterface
{
    /**
     * @var RepositoryInterface
     */
    protected $orderItemRepository;

    /**
     * @var RepositoryInterface
     */
    protected $variantRepository;

    /**
     * @var OriginatorInterface
     */
    protected $originator;

    /**
     * @param RepositoryInterface $orderItemRepository
     * @param RepositoryInterface $variantRepository
     * @param OriginatorInterface $originator
     */
    public function __construct(
        RepositoryInterface $orderItemRepository, 
        RepositoryInterface $variantRepository,
        OriginatorInterface $originator
    ) {
        $this->orderItemRepository = $orderItemRepository;
        $this->variantRepository   = $variantRepository;
        $this->originator          = $originator;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(PromotionSubjectInterface $subject, array $configuration, PromotionInterface $promotion)
    {
        if ($subject instanceof OrderItemInterface) {
            return;
        }

        if (!$subject instanceof OrderInterface) {
            throw new UnexpectedTypeException($subject, 'Sylius\Component\Order\Model\OrderInterface');
        }

        foreach ($subject->getItems() as $orderItem) {
            if ($promotion === $this->originator->getOrigin($orderItem)) {
                return;
            }
        }

        $subject->addItem($this->createItem($configuration, $promotion));
    }

    /**
     * {@inheritdoc}
     */
    public function revert(PromotionSubjectInterface $subject, array $configuration, PromotionInterface $promotion)
    {
        if ($subject instanceof OrderItemInterface) {
            return;
        }

        if (!$subject instanceof OrderInterface) {
            throw new UnexpectedTypeException($subject, 'Sylius\Component\Order\Model\OrderInterface');
        }

        foreach ($subject->getItems() as $orderItem) {
            if ($promotion === $this->originator->getOrigin($orderItem)) {
                $subject->removeItem($orderItem);

                return;
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getConfigurationFormType()
    {
        return 'sylius_promotion_action_add_product_configuration';
    }

    /**
     * Create promotion order item.
     *
     * @param array              $configuration
     * @param PromotionInterface $promotion
     *
     * @return OrderItemInterface
     */
    protected function createItem(array $configuration, PromotionInterface $promotion)
    {
        $variant = $this->variantRepository->find($configuration['variant']);

        $orderItem = $this->orderItemRepository->createNew();
        $orderItem->setVariant($variant);
        $orderItem->setQuantity((int) $configuration['quantity']);
        $orderItem->setUnitPrice((int) $configuration['price']);

        $this->originator->setOrigin($orderItem, $promotion);

        return $orderItem;
    }
}
