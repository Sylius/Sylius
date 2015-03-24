<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Affiliate\Action;

use Sylius\Component\Affiliate\Model\AffiliateInterface;
use Sylius\Component\Promotion\Action\AffiliationApplicatorInterface;
use Sylius\Component\Registry\ServiceRegistryInterface;

class AffiliationApplicator implements AffiliationApplicatorInterface
{
    protected $registry;

    public function __construct(ServiceRegistryInterface $registry)
    {
        $this->registry = $registry;
    }

    public function apply($subject, AffiliateInterface $affiliate)
    {
        /** @var $action AffiliateActionInterface */
        foreach ($this->registry->all() as $action) {
            $action->execute($subject, $action->getConfiguration(), $affiliate);
        }
    }
}
