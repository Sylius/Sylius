<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Page\Admin\StaticContent;

use Sylius\Behat\Page\Admin\Crud\CreatePageInterface as BaseCreatePageInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
interface CreatePageInterface extends BaseCreatePageInterface
{
    /**
     * @param string $title
     */
    public function setTitle($title);

    /**
     * @param string $internalName
     */
    public function setInternalName($internalName);
    
    /**
     * @param string $content
     */
    public function setContent($content);
}
