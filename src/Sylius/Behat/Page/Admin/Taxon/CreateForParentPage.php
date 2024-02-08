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

namespace Sylius\Behat\Page\Admin\Taxon;

class CreateForParentPage extends CreatePage implements CreateForParentPageInterface
{
    public function getRouteName(): string
    {
        return 'sylius_admin_taxon_create_for_parent';
    }
}
