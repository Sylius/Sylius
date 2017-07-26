<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\UserBundle\Authentication;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Authentication\DefaultAuthenticationSuccessHandler as SymfonyDefaultAuthenticationSuccessHandler;
use Symfony\Component\Security\Http\ParameterBagUtils;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

/**
 * TODO: Workaround for regression introduced in Symfony 3.3. To be deleted when fixed.
 *
 * @see https://github.com/symfony/symfony/pull/23411
 * @see https://github.com/symfony/symfony/pull/23061
 *
 * @internal
 *
 * {@inheritdoc}
 */
class DefaultAuthenticationSuccessHandler extends SymfonyDefaultAuthenticationSuccessHandler
{
    use TargetPathTrait;

    /**
     * {@inheritdoc}
     */
    protected function determineTargetUrl(Request $request)
    {
        if ($this->options['always_use_default_target_path']) {
            return $this->options['default_target_path'];
        }

        if ($targetUrl = ParameterBagUtils::getRequestParameterValue($request, $this->options['target_path_parameter'])) {
            return $targetUrl;
        }

        if (null !== $this->providerKey && $targetUrl = $this->getTargetPath($request->getSession(), $this->providerKey)) {
            $this->removeTargetPath($request->getSession(), $this->providerKey);

            return $targetUrl;
        }

        if ($this->options['use_referer'] && ($targetUrl = $request->headers->get('Referer')) && parse_url($targetUrl, PHP_URL_PATH) !== parse_url($this->httpUtils->generateUri($request, $this->options['login_path']), PHP_URL_PATH)) {
            return $targetUrl;
        }

        return $this->options['default_target_path'];
    }
}
