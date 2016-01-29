<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Controller;

use FOS\RestBundle\Controller\ExceptionController as BaseExceptionController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Log\DebugLoggerInterface;

/**
 * {@inheritdoc}
 *
 * BEWARE: This exception controller always shows the exception messages,
 * stacktraces, etc., no matter how %kernel.debug% is set
 *
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class VerboseExceptionController extends BaseExceptionController
{
    /**
     * {@inheritdoc}
     */
    public function showAction(Request $request, $exception, DebugLoggerInterface $logger = null)
    {
        $request->attributes->set('showException', true);

        return parent::showAction($request, $exception, $logger);
    }
}
