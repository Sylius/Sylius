<?php
/**
 * Created by PhpStorm.
 * User: mamazu
 * Date: 30/01/18
 * Time: 10:15
 */

declare(strict_types=1);

namespace Sylius\Component\Core\URLRedirect;

interface URLRedirectServiceInterface
{
    /**
     * Checks if the route has a redirect defined
     *
     * @param string $url
     *
     * @return bool
     */
    public function hasActiveRedirect(string $url): bool;

    /**
     * Gets the URL of the redirect
     *
     * @return null|string
     */
    public function getRedirect(): ?string;
}