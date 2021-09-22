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

namespace spec\Sylius\Bundle\CoreBundle\Listener;

use Doctrine\ORM\EntityManagerInterface;
use PhpSpec\ObjectBehavior;
use Sylius\Bundle\CoreBundle\Processor\CatalogPromotionReprocessorInterface;
use Sylius\Component\Promotion\Event\CatalogPromotionConfigurationRemoved;

final class CatalogPromotionConfigurationRemovedListenerSpec extends ObjectBehavior
{
    function let(
        CatalogPromotionReprocessorInterface $catalogPromotionReprocessor,
        EntityManagerInterface $entityManager
    ): void {
        $this->beConstructedWith($catalogPromotionReprocessor, $entityManager);
    }

    function it_removes_promotions(
        CatalogPromotionReprocessorInterface $catalogPromotionReprocessor,
        EntityManagerInterface $entityManager
    ): void {
        $catalogPromotionReprocessor->reprocess()->shouldBeCalled();

        $entityManager->flush()->shouldBeCalled();

        $this->__invoke(new CatalogPromotionConfigurationRemoved());
    }
}

