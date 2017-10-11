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

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Sylius\Component\Attribute\AttributeType\SelectAttributeType;
use Sylius\Component\Product\Model\ProductAttributeInterface;
use Sylius\Component\Product\Model\ProductAttributeValueInterface;
use Sylius\Component\Product\Repository\ProductAttributeValueRepositoryInterface;

final class SelectProductAttributeChoiceRemoveListener
{
    /**
     * @var string
     */
    private $productAttributeValueClass;

    /**
     * @param string $productAttributeValueClass
     */
    public function __construct(string $productAttributeValueClass)
    {
        $this->productAttributeValueClass = $productAttributeValueClass;
    }

    /**
     * @param LifecycleEventArgs $event
     */
    public function postUpdate(LifecycleEventArgs $event): void
    {
        /** @var ProductAttributeInterface $productAttribute */
        $productAttribute = $event->getEntity();

        if (!($productAttribute instanceof ProductAttributeInterface)) {
            return;
        }

        if ($productAttribute->getType() !== SelectAttributeType::TYPE) {
            return;
        }

        $entityManager = $event->getEntityManager();

        $unitOfWork = $entityManager->getUnitOfWork();
        $changeSet = $unitOfWork->getEntityChangeSet($productAttribute);

        $oldChoices = $changeSet['configuration'][0]['choices'] ?? [];
        $newChoices = $changeSet['configuration'][1]['choices'] ?? [];

        $removedChoices = array_diff_key($oldChoices, $newChoices);
        if (!empty($removedChoices)) {
            $this->removeValues($entityManager, array_keys($removedChoices));
        }
    }

    /**
     * @param ObjectManager $entityManager
     * @param array|string[] $choiceKeys
     */
    public function removeValues(ObjectManager $entityManager, array $choiceKeys): void
    {
        /** @var ProductAttributeValueRepositoryInterface $productAttributeValueRepository */
        $productAttributeValueRepository = $entityManager->getRepository($this->productAttributeValueClass);
        foreach ($choiceKeys as $choiceKey) {
            $productAttributeValues = $productAttributeValueRepository->findByJsonChoiceKey($choiceKey);

            /** @var ProductAttributeValueInterface $productAttributeValue */
            foreach ($productAttributeValues as $productAttributeValue) {
                $newValue = array_diff($productAttributeValue->getValue(), [$choiceKey]);
                if (!empty($newValue)) {
                    $productAttributeValue->setValue($newValue);

                    continue;
                }

                $entityManager->remove($productAttributeValue);
            }
        }

        $entityManager->flush();
    }
}
