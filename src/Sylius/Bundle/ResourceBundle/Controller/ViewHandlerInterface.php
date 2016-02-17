<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ResourceBundle\Controller;

use FOS\RestBundle\View\View;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface ViewHandlerInterface
{
    /**
     * @param RequestConfiguration $requestConfiguration
     * @param View $view
     *
     * @return mixed
     */
    public function handle(RequestConfiguration $requestConfiguration, View $view);
}
