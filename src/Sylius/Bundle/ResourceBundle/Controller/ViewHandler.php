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
use FOS\RestBundle\View\ViewHandler as RestViewHandler;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class ViewHandler implements ViewHandlerInterface
{
    /**
     * @var RestViewHandler
     */
    private $restViewHandler;

    /**
     * @param RestViewHandler $restViewHandler
     */
    public function __construct(RestViewHandler $restViewHandler)
    {
        $this->restViewHandler = $restViewHandler;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(RequestConfiguration $requestConfiguration, View $view)
    {
        if (!$requestConfiguration->isHtmlRequest()) {
            $this->restViewHandler->setExclusionStrategyGroups($requestConfiguration->getSerializationGroups());

            if ($version = $requestConfiguration->getSerializationVersion()) {
                $this->restViewHandler->setExclusionStrategyVersion($version);
            }

            $view->getSerializationContext()->enableMaxDepthChecks();
        }

        return $this->restViewHandler->handle($configuration, $view);
    }
}
