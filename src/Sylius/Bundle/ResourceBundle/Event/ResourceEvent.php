<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ResourceBundle\Event;

use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\Response;

/**
 * Resource event.
 *
 * @author Jérémy Leherpeur <jeremy@lehepeur.net>
 */
class ResourceEvent extends GenericEvent
{
    /**
     * Set response property
     *
     * @var Response
     */
    protected $response;

    public function setResponse(Response $response)
    {
        $this->response = $response;
    }

    /**
     * Get response property
     *
     * @return Response $response
     */
    public function getResponse()
    {
        return $this->response;
    }
}
