<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\CoreBundle\Fixture;

use Symfony\Component\HttpFoundation\Request;

class RequestStack
{
    private $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function getCurrentRequest()
    {
        return $this->request;
    }
}
