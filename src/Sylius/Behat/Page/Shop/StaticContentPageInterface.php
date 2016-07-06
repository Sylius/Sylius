<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Page\Shop;

use Sylius\Behat\Page\SymfonyPageInterface;
use Sylius\Bundle\ContentBundle\Document\StaticContent;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
interface StaticContentPageInterface extends SymfonyPageInterface
{
    /**
     * @param string $path
     */
    public function access($path);

    /**
     * @param StaticContent $staticContent
     *
     * @throws \InvalidArgumentException
     */
    public function assertPageHasContent(StaticContent $staticContent);
}
