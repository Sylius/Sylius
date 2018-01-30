<?php
/**
 * Created by PhpStorm.
 * User: mamazu
 * Date: 30/01/18
 * Time: 13:15
 */

declare(strict_types=1);

namespace Sylius\Component\Core\URLRedirect;


use Sylius\Component\Core\Model\URLRedirectInterface;

/**
 * Interface URLRedirectLoopDetectorInterface
 *
 * Classes that detect loops in URL redirects can implement that.
 *
 * @package Sylius\Component\Core\URLRedirect
 */
interface URLRedirectLoopDetectorInterface
{
    public function containsLoop(URLRedirectInterface $newNode): bool;
}