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

namespace spec\Sylius\Bundle\ProductBundle\Remover;

use Doctrine\Common\Persistence\ObjectManager;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Product\Model\ProductAttributeValueInterface;
use Sylius\Component\Product\Repository\ProductAttributeValueRepositoryInterface;

final class SelectProductAttributeValuesRemoverSpec extends ObjectBehavior
{
    function let(
        ProductAttributeValueRepositoryInterface $productAttributeValueRepository,
        ObjectManager $productAttributeValueManager
    ): void {
        $this->beConstructedWith($productAttributeValueRepository, $productAttributeValueManager);
    }

    function it_removes_choices_from_product_attribute_values(
        ProductAttributeValueRepositoryInterface $productAttributeValueRepository,
        ObjectManager $productAttributeValueManager,
        ProductAttributeValueInterface $productAttributeValue
    ): void {
        $productAttributeValueRepository
            ->findByJsonChoiceKey('1739bc61-9e42-4c80-8b9a-f97f0579cccb')
            ->willReturn([$productAttributeValue])
        ;

        $productAttributeValue->getValue()->willReturn([
            '8ec40814-adef-4194-af91-5559b5f19236',
            '1739bc61-9e42-4c80-8b9a-f97f0579cccb',
        ]);

        $productAttributeValue->setValue(['8ec40814-adef-4194-af91-5559b5f19236'])->shouldBeCalled();
        $productAttributeValueManager->flush()->shouldBeCalled();

        $this->removeValues(['1739bc61-9e42-4c80-8b9a-f97f0579cccb']);
    }

    function it_removes_product_attribute_values(
        ProductAttributeValueRepositoryInterface $productAttributeValueRepository,
        ObjectManager $productAttributeValueManager,
        ProductAttributeValueInterface $productAttributeValue
    ): void {
        $productAttributeValueRepository
            ->findByJsonChoiceKey('1739bc61-9e42-4c80-8b9a-f97f0579cccb')
            ->willReturn([$productAttributeValue])
        ;

        $productAttributeValue->getValue()->willReturn(['1739bc61-9e42-4c80-8b9a-f97f0579cccb']);

        $productAttributeValueManager->remove($productAttributeValue)->shouldBeCalled();
        $productAttributeValueManager->flush()->shouldBeCalled();

        $this->removeValues(['1739bc61-9e42-4c80-8b9a-f97f0579cccb']);
    }
}
