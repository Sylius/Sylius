<?php

/*
 * This file is part of the Flux:: shop sources.
 * COPYRIGHT (C) 2024, HARMAN INTERNATIONAL. ALL RIGHTS RESERVED.
 * -----------------------------------------------------------------------------
 * CONFIDENTIAL: NO PART OF THIS DOCUMENT MAY BE REPRODUCED IN ANY FORM WITHOUT THE
 * EXPRESSED WRITTEN PERMISSION OF HARMAN INTERNATIONAL.
 * DO NOT DISCLOSE ANY INFORMATION CONTAINED IN THIS DOCUMENT TO ANY THIRD-PARTY
 * WITHOUT THE PRIOR WRITTEN CONSENT OF HARMAN INTERNATIONAL.
 *
 */

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\PaymentRequest\Provider;

interface ServiceProviderAwareProviderInterface extends HttpResponseProviderInterface
{
    public function getHttpResponseProvider(string $index): ?HttpResponseProviderInterface;

    /** @return string[] */
    public function getProviderIndex(): array;
}
