<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\BackendBundle\Behat;

use Sylius\Bundle\ResourceBundle\Behat\DefaultContext;

/**
 * Web context.
 *
 * @author Julien Boyer <julien@meetserious.com>
 */
class BackendContext extends DefaultContext
{
    /**
     * @Given /^I am on the dashboard$/
     */
    public function iAmOnDashboard()
    {
        $this->getSession()->visit($this->generateUrl('sylius_backend_index'));
    }
}
