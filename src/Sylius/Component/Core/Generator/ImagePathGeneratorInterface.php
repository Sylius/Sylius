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

namespace Sylius\Component\Core\Generator;

use Sylius\Component\Core\Model\ImageInterface;

interface ImagePathGeneratorInterface
{
    public function generate(ImageInterface $image): string;
}
