<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Behat\Page;

use SensioLabs\Behat\PageObjectExtension\PageObject\Page;
use SensioLabs\Behat\PageObjectExtension\PageObject\Exception\PathNotProvidedException;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
abstract class ExternalPage extends Page
{
    public function assertRoute(array $urlParameters = array())
    {
        $this->verify($urlParameters);
    }

    protected function getUrl(array $urlParameters = array())
    {
        if (null === $this->getAbsolutePath()) {
            throw new \RuntimeException('You need to provide absolute path, null given');
        }

        return $this->getAbsolutePath();
    }

    protected function verifyUrl(array $urlParameters = array())
    {
        $pos = strpos($this->getSession()->getCurrentUrl(), $this->getUrl($urlParameters));
        if (0 !== $pos) {
            throw new UnexpectedPageException(sprintf('Expected to be on "%s" but found "%s" instead', $this->getUrl($urlParameters), $this->getSession()->getCurrentUrl()));
        }
    }

    /**
     * @return string
     */
    abstract protected function getAbsolutePath();
}
