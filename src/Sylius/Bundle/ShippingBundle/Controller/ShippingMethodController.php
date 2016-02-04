<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ShippingBundle\Controller;

use Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException;
use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
class ShippingMethodController extends ResourceController
{
    /**
     * @param Request $request
     *
     * @return Response
     */
    public function deleteAction(Request $request)
    {
        try {
            return parent::deleteAction($request);
        } catch (ForeignKeyConstraintViolationException $exception) {
            $referer = $this->requestConfigurationFactory->create($this->metadata, $request);
            $this->addFlash('error', sprintf('Cannot delete, the shipping method is in use.'));

            return $this->redirectHandler->redirectToReferer($referer);
        }
    }
}
