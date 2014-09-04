<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\MoneyBundle\ExchangeRate\Provider;

use Exception;

/**
 * Class ProviderException
 *
 * General Provider Exception used when service is not available or XML is not valid
 *
 * @author Ivan Djurdjevac <djurdjevac@gmail.com>
 */
class ProviderException extends Exception {}
