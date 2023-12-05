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

namespace Sylius\Tests\Functional\app;

use App\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

class AppKernel extends Kernel
{
    public function __construct(string $environment, bool $debug, private ?string $testCase = null)
    {
        parent::__construct($environment, $debug);
    }

    public function getCacheDir(): string
    {
        return sys_get_temp_dir() . sprintf('/sylius_functional_tests/%s/cache/%s', $this->testCase ?? '', $this->environment);
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        parent::registerContainerConfiguration($loader);

        if (null !== $this->testCase) {
            $loader->load(__DIR__ . sprintf('/%s/config.yml', $this->testCase));
        }
    }
}
