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

namespace Sylius\Behat\Page\Admin\ProductAssociationType;

use Sylius\Behat\Page\Admin\Crud\IndexPageInterface as BaseIndexPageInterface;

interface IndexPageInterface extends BaseIndexPageInterface
{
    /**
     * @param string $field
     * @param string $type
     */
    public function specifyFilterType(string $field, string $type): void;

    /**
     * @param string $field
     * @param string $value
     */
    public function specifyFilterValue(string $field, string $value): void;
}
