<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\LocaleBundle\Twig;

use Sylius\Component\Locale\Context\LocaleContextInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class LocaleExtension extends \Twig_Extension
{
    /**
     * @var LocaleContextInterface
     */
    private $localeContext;

    /**
     * @param LocaleContextInterface $localeContext
     */
    public function __construct(LocaleContextInterface $localeContext)
    {
        $this->localeContext = $localeContext;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFunction('sylius_locale', array($this, 'getCurrentLocale')),
        );
    }

    /**
     * @return string
     */
    public function getCurrentLocale()
    {
        return $this->localeContext->getCurrentLocale();
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_locale';
    }
}
