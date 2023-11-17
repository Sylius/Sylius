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

namespace Sylius\Bundle\LocaleBundle\Doctrine\ORM;

use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Component\Locale\Repository\LocaleRepositoryInterface;

/**
 * @template T of LocaleInterface
 *
 * @implements LocaleRepositoryInterface<T>
 */
class LocaleRepository extends EntityRepository implements LocaleRepositoryInterface
{
}
