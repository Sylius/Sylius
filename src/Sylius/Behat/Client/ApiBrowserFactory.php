<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Behat\Client;

use Symfony\Component\BrowserKit\AbstractBrowser;

final readonly class ApiBrowserFactory
{
    /**
     * @param AbstractBrowser<object, object> $kernelBrowser
     */
    public function __construct(
        private AbstractBrowser $kernelBrowser,
        private WorkerHttpBrowser $workerHttpBrowser,
    ) {
    }

    /**
     * @return AbstractBrowser<object, object>
     */
    public function createBrowser(): AbstractBrowser
    {
        // @phpstan-ignore return.type
        return getenv('SYLIUS_BEHAT_API_WORKER') === 'true'
            ? $this->workerHttpBrowser
            : $this->kernelBrowser
        ;
    }
}
