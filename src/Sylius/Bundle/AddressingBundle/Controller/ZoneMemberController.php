<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Sylius\Bundle\AddressingBundle\Controller;

use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
class ZoneMemberController extends ResourceController
{
    /**
     * {@inheritdoc}
     */
    public function createNew()
    {
        $request = $this->getRequest();
        if (null === $zoneId = $request->get('zoneId')) {
            throw new NotFoundHttpException('No country given');
        }

        $zone = $this
            ->getZoneController()
            ->findOr404($request, array('id' => $zoneId))
        ;

        $zoneMember = parent::createNew();
        $zoneMember->setBelongsTo($zone);

        return $zoneMember;
    }

    /**
     * @return ResourceController
     */
    protected function getZoneController()
    {
        return $this->get('sylius.controller.zone');
    }
}
