<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Affiliate\Context;

use Sylius\Component\Affiliate\Model\AffiliateInterface;

/**
 * Default affiliate context implementation.
 *
 * @author Laszlo Horvath <pentarim@gmail.com>
 */
class AffiliateContext implements AffiliateContextInterface
{
    /**
     * @var AffiliateInterface
     */
    protected $affiliate;

    /**
     * {@inheritdoc}
     */
    public function getAffiliate()
    {
        return $this->affiliate;
    }

    /**
     * {@inheritdoc}
     */
    public function setAffiliate(AffiliateInterface $affiliate)
    {
        $this->affiliate = $affiliate;

        return $this;
    }

    /**
     * Does the context contain an affiliate.
     *
     * @return bool
     */
    public function hasAffiliate()
    {
        return $this->getAffiliate() instanceof AffiliateInterface;
    }
}
