<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Middleware\Locale;

use Negotiation\LanguageNegotiator;
use Sylius\Bundle\SettingsBundle\Manager\SettingsManagerInterface;
use Sylius\Bundle\SettingsBundle\Model\Settings;
use Sylius\Component\Core\Model\UserInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernelInterface;

/**
 * @author Joseph Bielawski <stloyd@gmail.com>
 */
class NegotiateLocale implements HttpKernelInterface
{
    /**
     * The wrapped kernel implementation.
     *
     * @var HttpKernelInterface
     */
    private $app;

    /**
     * @var Settings
     */
    private $settings;

    public function __construct(HttpKernelInterface $app, SettingsManagerInterface $settingsManager)
    {
        $this->app      = $app;
        // @todo change this after refactoring of SettingsBundle
        // $this->settings = $settingsManager->loadSettings('general');
    }

    /**
     * {@inheritdoc}
     */
    public function handle(Request $request, $type = HttpKernelInterface::MASTER_REQUEST, $catch = true)
    {
        $locale = null;

        // Detect user is logged-in & try to use his settings
        // @todo this will not (now) work as expected because session is lazy loaded
        if ($request->hasSession() && $request->getSession()->has('_security_user')) {
            /* @var $user UserInterface */
            $user = unserialize($request->getSession()->get('_security_user'));
            if ($user) {
                // @todo Use locale set at user model
                $locale = 'en';
            }
        }

        if (!$locale) {
            // Fallback to header based locale detection.
            // Original code by William Durand
            // @link https://github.com/willdurand/StackNegotiation
            if (null !== $locale = $request->headers->get('Accept-Language')) {
                $languageNegotiator = new LanguageNegotiator();
                $locale = $languageNegotiator->getBest($locale);
                $request->attributes->set('_accept_language', $locale);

                if (null !== $locale) {
                    $locale = $locale->getValue();
                }
            }

            // Check that detected locale is supported by shop
            //$locales = $this->settings->get('enabled_locales', array());
            $locales = array();
            if (null === $locale || !in_array($locale, $locales)) {
                $locale = null;
            }
        }

        // Fallback to default shop locale if was not detected
        if (!$locale) {
            $locale = 'en';//$this->settings->get('locale');
        }

        $request->attributes->set('_locale', $locale);
        $request->setDefaultLocale($locale);

        return $this->app->handle($request, $type, $catch);
    }
}
