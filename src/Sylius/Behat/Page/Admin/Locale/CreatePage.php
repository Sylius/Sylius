<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Page\Admin\Locale;

use Sylius\Behat\Page\Admin\Crud\CreatePage as BaseCreatePage;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class CreatePage extends BaseCreatePage implements CreatePageInterface
{
    /**
     * {@inheritdoc}
     */
    public function chooseName($name)
    {
        $this->getDocument()->selectFieldOption('Name', $name);
    }
}
