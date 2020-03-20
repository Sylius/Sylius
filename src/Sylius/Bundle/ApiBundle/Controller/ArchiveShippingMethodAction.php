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

namespace Sylius\Bundle\ApiBundle\Controller;

use Sylius\Component\Core\Model\ShippingMethodInterface;

final class ArchiveShippingMethodAction
{
    public function __invoke(ShippingMethodInterface $data): ShippingMethodInterface
    {
        $data->setArchivedAt(new \DateTime());

        return $data;
    }
}
