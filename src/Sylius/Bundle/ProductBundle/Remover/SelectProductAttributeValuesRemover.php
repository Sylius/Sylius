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

namespace Sylius\Bundle\ProductBundle\Remover;

use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Component\Product\Model\ProductAttributeValueInterface;
use Sylius\Component\Product\Repository\ProductAttributeValueRepositoryInterface;

final class SelectProductAttributeValuesRemover implements SelectProductAttributeValuesRemoverInterface
{
    /**
     * @var ProductAttributeValueRepositoryInterface
     */
    private $productAttributeValueRepository;

    /**
     * @var ObjectManager
     */
    private $productAttributeValueManager;

    /**
     * @param ProductAttributeValueRepositoryInterface $productAttributeValueRepository
     * @param ObjectManager $productAttributeValueManager
     */
    public function __construct(
        ProductAttributeValueRepositoryInterface $productAttributeValueRepository,
        ObjectManager $productAttributeValueManager
    ) {
        $this->productAttributeValueRepository = $productAttributeValueRepository;
        $this->productAttributeValueManager = $productAttributeValueManager;
    }

    /**
     * {@inheritdoc}
     */
    public function removeValues(array $choiceKeys): void
    {
        foreach ($choiceKeys as $choiceKey) {
            $productAttributeValues = $this->productAttributeValueRepository->findByJsonChoiceKey($choiceKey);

            /** @var ProductAttributeValueInterface $productAttributeValue */
            foreach ($productAttributeValues as $productAttributeValue) {
                $newValue = array_diff($productAttributeValue->getValue(), [$choiceKey]);
                if (!empty($newValue)) {
                    $productAttributeValue->setValue($newValue);

                    continue;
                }

                $this->productAttributeValueManager->remove($productAttributeValue);
            }

            $this->productAttributeValueManager->flush();
        }
    }
}
