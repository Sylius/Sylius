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
use Sylius\Component\Resource\Exception\UnexpectedTypeException;

interface AffiliateActionInterface
{
    /**
     * Applies the transaction to its affiliation.
     *
     * @param object             $subject
     * @param array              $configuration
     * @param AffiliateInterface $affiliate
     *
     * @throws UnexpectedTypeException
     */
    public function execute($subject, array $configuration, AffiliateInterface $affiliate);

    /**
     * Returns the form name related to this action.
     *
     * @return string
     */
    public function getConfigurationFormType();
}
