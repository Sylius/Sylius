<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Sylius\Behat\Service;

use Sylius\Behat\Page\Admin\Crud\CreatePageInterface;
use Sylius\Behat\Page\Admin\Crud\PageWithFormInterface;
use Sylius\Behat\Page\Admin\Crud\UpdatePageInterface;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
interface CurrentPageResolverInterface
{
    /**
     * @param CreatePageInterface $createPage
     * @param UpdatePageInterface $updatePage
     *
     * @return PageWithFormInterface
     */
    public function getCurrentPageWithForm(CreatePageInterface $createPage, UpdatePageInterface $updatePage);
}
