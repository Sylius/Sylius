<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Page\Admin\Taxon;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class CreateForParentPage extends CreatePage implements CreateForParentPageInterface
{
    /**
     * {@inheritdoc}
     */
    public function getRouteName()
    {
        return 'sylius_admin_taxon_create_for_parent';
    }
}
