<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Middleware\CookieGuard;

use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;

/**
 * Encrypts outgoing cookies, decodes incoming ones.
 *
 * Code inspired by the Laravel Cookie Guard component.
 *
 * @author Joseph Bielawski <stloyd@gmail.com>
 */
class CookieGuard implements HttpKernelInterface
{
    /**
     * The wrapped kernel implementation.
     *
     * @var HttpKernelInterface
     */
    private $app;

    /**
     * @var string
     */
    private $key;

    /**
     * @var integer
     */
    private $keySize;

    /**
     * @var integer
     */
    private $iv;

    /**
     * @var integer
     */
    private $ivSize;

    /**
     * Create a new CookieGuard instance.
     *
     * @param HttpKernelInterface $app
     */
    public function __construct(HttpKernelInterface $app)
    {
        $this->app     = $app;

        $this->key     = pack('H*', 'bcb04b7e103a0cd8b54763051cef08bc55abe029fdebae5e1d417e2ffb2a00a3');
        $this->keySize = strlen($this->key);

        $this->ivSize  = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
        $this->iv      = mcrypt_create_iv($this->ivSize, MCRYPT_RAND);
    }

    /**
     * {@inheritdoc}
     */
    public function handle(Request $request, $type = HttpKernelInterface::MASTER_REQUEST, $catch = true)
    {
        return $this->encryptResponse($this->app->handle($this->decryptRequest($request), $type, $catch));
    }

    /**
     * Decrypt the cookies on the request.
     *
     * @param Request $request
     *
     * @return Request
     */
    protected function decryptRequest(Request $request)
    {
        foreach ($request->cookies as $key => $cookie) {
            $request->cookies->set($key, $this->decryptCookie($cookie));
        }

        return $request;
    }

    /**
     * Encrypt the cookies on an outgoing response.
     *
     * @param Response $response
     *
     * @return Response
     */
    protected function encryptResponse(Response $response)
    {
        /* @var $cookie Cookie */
        foreach (array_keys($response->headers->getCookies()) as $cookie) {
            $response->headers->setCookie($this->duplicate($cookie, $this->encrypt($cookie->getValue())));
        }

        return $response;
    }

    /**
     * Decrypt the given cookie and return the value.
     *
     * @param string|array $cookie
     *
     * @return array
     */
    protected function decryptCookie($cookie)
    {
        $decrypted = array();

        foreach ((array) $cookie as $key => $value) {
            $decrypted[$key] = $this->decrypt($value);
        }

        return $decrypted;
    }

    /**
     * Decrypt the given cookie and return the value.
     *
     * @param string $cookie
     *
     * @return array
     */
    protected function encrypt($cookie)
    {
        return base64_encode($this->iv. mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $this->key, $cookie, MCRYPT_MODE_CBC, $this->iv));
    }

    /**
     * Decrypt the given cookie and return the value.
     *
     * @param string $encodedCookie
     *
     * @return array
     */
    protected function decrypt($encodedCookie)
    {
        $decoded = base64_decode($encodedCookie);

        return trim(mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $this->key, substr($decoded, $this->ivSize), MCRYPT_MODE_CBC, substr($decoded, 0, $this->ivSize)));
    }

    /**
     * Duplicate a cookie with a new value.
     *
     * @param Cookie $cookie
     * @param mixed  $value
     *
     * @return Cookie
     */
    protected function duplicate(Cookie $cookie, $value)
    {
        return new Cookie(
            $cookie->getName(), $value, $cookie->getExpiresTime(), $cookie->getPath(),
            $cookie->getDomain(), $cookie->isSecure(), $cookie->isHttpOnly()
        );
    }
}
