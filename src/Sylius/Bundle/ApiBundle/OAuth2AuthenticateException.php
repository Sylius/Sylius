<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ApiBundle;

use OAuth2\OAuth2AuthenticateException as BaseAuthenticationException;

/**
 * @see \OAuth2\OAuth2ServerException
 * @see \OAuth2\OAuth2AuthenticateException
 *
 * @author Jan Góralski <jan.goralski@lakion.com>
 */
final class OAuth2AuthenticateException extends BaseAuthenticationException
{
    /**
     * {@inheritdoc}
     */
    public function __construct($httpCode, $tokenType, $realm, $error, $errorDescription = null, $scope = null)
    {
        parent::__construct($httpCode, $tokenType, $realm, $error, $errorDescription, $scope);

        $this->formatErrorData();
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return $this->errorData['message'];
    }

    private function formatErrorData()
    {
        $this->errorData['code'] = $this->httpCode;
        $this->errorData['message'] = $this->errorData['error_description'];

        unset($this->errorData['error_description']);
    }
}
