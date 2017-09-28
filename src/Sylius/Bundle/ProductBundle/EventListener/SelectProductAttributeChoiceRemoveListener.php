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

namespace Sylius\Bundle\ProductBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Sylius\Bundle\ProductBundle\Remover\SelectProductAttributeValuesRemoverInterface;
use Sylius\Component\Attribute\AttributeType\SelectAttributeType;
use Sylius\Component\Product\Model\ProductAttributeInterface;

final class SelectProductAttributeChoiceRemoveListener
{
    /**
     * @var SelectProductAttributeValuesRemoverInterface
     */
    private $selectProductAttributeValuesRemover;

    /**
     * @param SelectProductAttributeValuesRemoverInterface $selectProductAttributeValuesRemover
     */
    public function __construct(SelectProductAttributeValuesRemoverInterface $selectProductAttributeValuesRemover)
    {
        $this->selectProductAttributeValuesRemover = $selectProductAttributeValuesRemover;
    }

    /**
     * @param LifecycleEventArgs $event
     */
    public function postUpdate(LifecycleEventArgs $event): void
    {
        /** @var ProductAttributeInterface $productAttribute */
        $productAttribute = $event->getEntity();

        if (
            $productAttribute instanceof ProductAttributeInterface &&
            $productAttribute->getType() === SelectAttributeType::TYPE
        ) {
            $unitOfWork = $event->getEntityManager()->getUnitOfWork();
            $changeSet = $unitOfWork->getEntityChangeSet($productAttribute);

            if (
                empty($changeSet['configuration'][0]['choices']) ||
                empty($changeSet['configuration'][1]['choices'])
            ) {
                return;
            }

            $oldChoices = $changeSet['configuration'][0]['choices'];
            $newChoices = $changeSet['configuration'][1]['choices'];

            $removedChoices = array_diff_key($oldChoices, $newChoices);
            if (!empty($removedChoices)) {
                $this->selectProductAttributeValuesRemover->removeValues(array_keys($removedChoices));
            }
        }
    }
}
