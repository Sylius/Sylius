<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\PayumBundle\Action\Offline;

use Payum\Core\Action\ActionInterface;
use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\Request\GetStatusInterface;
use Payum\Offline\Constants;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
final class StatusAction implements ActionInterface
{
    /**
     * {@inheritdoc}
     */
    public function execute($request)
    {
        /** @var $request GetStatusInterface */
        RequestNotSupportedException::assertSupports($this, $request);

        $model = ArrayObject::ensureArrayObject($request->getModel());

        if (false == $model[Constants::FIELD_STATUS]) {
            $request->markNew();

            return;
        }

        if (Constants::STATUS_PENDING == $model[Constants::FIELD_STATUS]) {
            $request->markNew();

            return;
        }

        if (Constants::STATUS_AUTHORIZED == $model[Constants::FIELD_STATUS]) {
            $request->markAuthorized();

            return;
        }

        if (Constants::STATUS_CAPTURED == $model[Constants::FIELD_STATUS]) {
            $request->markCaptured();

            return;
        }

        if (Constants::STATUS_CANCELED == $model[Constants::FIELD_STATUS]) {
            $request->markCanceled();

            return;
        }

        $request->markUnknown();
    }

    /**
     * {@inheritdoc}
     */
    public function supports($request)
    {
        return
            $request instanceof GetStatusInterface &&
            $request->getModel() instanceof \ArrayAccess
        ;
    }
}
