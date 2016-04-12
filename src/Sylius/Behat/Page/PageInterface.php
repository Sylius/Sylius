<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Page;

/**
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
interface PageInterface
{
    /**
     * @param array $urlParameters
     *
     * @throws UnexpectedPageException If page is not opened successfully
     */
    public function open(array $urlParameters = []);

    /**
     * @param array $urlParameters
     */
    public function tryToOpen(array $urlParameters = []);

    /**
     * @param array $urlParameters
     *
     * @throws UnexpectedPageException
     */
    public function verify(array $urlParameters = []);

    /**
     * @param array $urlParameters
     *
     * @return bool
     */
    public function isOpen(array $urlParameters = []);
}
