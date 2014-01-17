<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Promotion\Action;

use Sylius\Bundle\CoreBundle\Model\VariantInterface;
use Sylius\Bundle\CoreBundle\Model\OrderItemInterface;
use Sylius\Bundle\PromotionsBundle\Action\PromotionActionInterface;
use Sylius\Bundle\PromotionsBundle\Model\PromotionInterface;
use Sylius\Bundle\PromotionsBundle\Model\PromotionSubjectInterface;
use Sylius\Bundle\ResourceBundle\Model\RepositoryInterface;

/**
 * Free product action.
 *
 * @author Alexandre Bacco <alexandre.bacco@gmail.com>
 */
class AddProductAction implements PromotionActionInterface
{
    /**
     * OrderItem repository.
     *
     * @var RepositoryInterface
     */
    protected $itemRepository;

    /**
     * Variant repository.
     *
     * @var RepositoryInterface
     */
    protected $variantRepository;

    /**
     * Constructor.
     *
     * @param RepositoryInterface $itemRepository
     * @param RepositoryInterface $variantRepository
     */
    public function __construct(RepositoryInterface $itemRepository, RepositoryInterface $variantRepository)
    {
        $this->itemRepository    = $itemRepository;
        $this->variantRepository = $variantRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(PromotionSubjectInterface $subject, array $configuration, PromotionInterface $promotion)
    {
        $variant = $this->variantRepository->find($configuration['variant']);

        if (null !== $this->getItemFromSubject($subject, $configuration, $variant)) {
            return;
        }

        $item = $this->itemRepository->createNew();

        $item->setVariant($variant);
        $item->setQuantity($configuration['quantity']);
        $item->setUnitPrice($configuration['price']);

        $subject->addItem($item);
    }

    /**
     * {@inheritdoc}
     */
    public function revert(PromotionSubjectInterface $subject, array $configuration, PromotionInterface $promotion)
    {
        $item = $this->getItemFromSubject($subject, $configuration);

        if (null !== $item) {
            $subject->removeItem($item);
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
     * Return item added by promotion, null if it does not exist
     *
     * @param PromotionSubjectInterface $subject
     * @param array                     $configuration
     * @param VariantInterface          $variant
     * @return OrderItemInterface|null
     */
    protected function getItemFromSubject(PromotionSubjectInterface $subject, array $configuration, VariantInterface $variant = null)
    {
        if (null === $variant) {
            $variant = $this->variantRepository->find($configuration['variant']);
        }

        foreach ($subject->getItems() as $item) {
            if ($item->getVariant() === $variant
                && $item->getUnitPrice() === $configuration['price']
                && $item->getQuantity() === $configuration['quantity']
            ) {
                return $item;
            }
        }

        return null;
    }
}
