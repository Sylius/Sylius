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
use Sylius\Bundle\PromotionsBundle\Action\PromotionActionInterface;
use Sylius\Bundle\PromotionsBundle\Model\PromotionInterface;
use Sylius\Bundle\PromotionsBundle\Model\PromotionSubjectInterface;
use Sylius\Bundle\ResourceBundle\Model\RepositoryInterface;

/**
 * Free product action.
 *
 * @author Alexandre Bacco <alexandre.bacco@gmail.com>
 */
class FreeProductAction implements PromotionActionInterface
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
     * @param RepositoryInterface $repository
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

        if ($this->subjectHasItem($subject, $variant)) {
            return;
        }

        $item = $this->itemRepository->createNew();

        $item->setVariant($variant);
        $item->setQuantity($configuration['quantity']);
        $item->setUnitPrice(0);

        $subject->addItem($item);
    }

    /**
     * {@inheritdoc}
     */
    public function getConfigurationFormType()
    {
        return 'sylius_promotion_action_free_product_configuration';
    }

    protected function subjectHasItem(PromotionSubjectInterface $subject, VariantInterface $variant)
    {
        foreach ($subject->getItems() as $item) {
            if ($item->getVariant() === $variant && $item->getUnitPrice() === 0) {
                return true;
            }
        }

        return false;
    }
}