<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\WebBundle\Controller\Frontend;

use Sylius\Bundle\WebBundle\Controller\WebController;
use Symfony\Component\HttpFoundation\Response;

/**
 * Frontend homepage controller.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class HomepageController extends WebController
{
    /**
     * Store front page.
     *
     * @return Response
     */
    public function mainAction()
    {
        return $this->render($this->getTemplate('frontend_homepage'));
    }
}
