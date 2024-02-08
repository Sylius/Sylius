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

namespace Sylius\Behat\Element\Product\ShowPage;

interface MoreDetailsElementInterface
{
    public function getName(): string;

    public function getDescription(): string;

    public function getProductMetaKeywords(): string;

    public function getShortDescription(): string;

    public function getMetaDescription(): string;

    public function getSlug(): string;
}
