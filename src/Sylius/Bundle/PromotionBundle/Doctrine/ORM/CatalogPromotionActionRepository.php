<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\PromotionBundle\Doctrine\ORM;

use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Promotion\Model\CatalogPromotionActionInterface;
use Sylius\Component\Promotion\Repository\CatalogPromotionActionRepositoryInterface;

/**
 * @template T of CatalogPromotionActionInterface
 *
 * @implements CatalogPromotionActionRepositoryInterface<T>
 */
class CatalogPromotionActionRepository extends EntityRepository implements CatalogPromotionActionRepositoryInterface
{
}
