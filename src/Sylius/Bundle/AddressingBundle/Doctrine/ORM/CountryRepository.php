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

namespace Sylius\Bundle\AddressingBundle\Doctrine\ORM;

use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Addressing\Model\CountryInterface;
use Sylius\Component\Addressing\Repository\CountryRepositoryInterface;

/**
 * @implements CountryRepositoryInterface<CountryInterface>
 */
class CountryRepository extends EntityRepository implements CountryRepositoryInterface
{
}
