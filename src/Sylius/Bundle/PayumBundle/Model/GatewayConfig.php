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

namespace Sylius\Bundle\PayumBundle\Model;

use Payum\Core\Security\CypherInterface;
use Sylius\Component\Payment\Model\GatewayConfig as BaseGatewayConfig;

class GatewayConfig extends BaseGatewayConfig implements GatewayConfigInterface
{
    /** @var array<string, mixed> $decryptedConfig */
    protected array $decryptedConfig;

    public function __construct()
    {
        parent::__construct();

        $this->decryptedConfig = [];
    }

    public function getConfig(): array
    {
        if (isset($this->config['encrypted'])) {
            return $this->decryptedConfig;
        }

        return $this->config;
    }

    public function setConfig(array $config): void
    {
        $this->config = $config;
        $this->decryptedConfig = $config;
    }

    /** This method is based on Payum\Core\Model\GatewayConfig::decrypt */
    public function decrypt(CypherInterface $cypher): void
    {
        if (empty($this->config['encrypted'])) {
            return;
        }

        foreach ($this->config as $name => $value) {
            if ('encrypted' == $name || is_bool($value)) {
                $this->decryptedConfig[$name] = $value;

                continue;
            }

            $this->decryptedConfig[$name] = $cypher->decrypt($value);
        }
    }

    /** This method is based on Payum\Core\Model\GatewayConfig::encrypt */
    public function encrypt(CypherInterface $cypher): void
    {
        $this->decryptedConfig['encrypted'] = true;

        foreach ($this->decryptedConfig as $name => $value) {
            if ('encrypted' == $name || is_bool($value)) {
                $this->config[$name] = $value;

                continue;
            }

            $this->config[$name] = $cypher->encrypt($value);
        }
    }
}
