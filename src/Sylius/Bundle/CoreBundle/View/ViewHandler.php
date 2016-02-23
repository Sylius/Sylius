<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\View;

use FOS\RestBundle\View\ViewHandler as BaseViewHandler;
use FOS\RestBundle\View\View;
use Sylius\Bundle\ApiBundle\Serializer\Exclusion\SparseFieldsetsExclusionStrategy;

class ViewHandler extends BaseViewHandler
{
    /**
     * {@inheritDoc}
     */
    protected function getSerializationContext(View $view)
    {
        $request = $this->container->get('request_stack')->getCurrentRequest();

        if ($request->isMethod('GET') && $request->query->has('include')) {
            $context = $view->getSerializationContext();
            $context->addExclusionStrategy(
                new SparseFieldsetsExclusionStrategy($request->query->get('include'))
            );
            $view->setSerializationContext($context);
        }

        return parent::getSerializationContext($view);
    }
}
