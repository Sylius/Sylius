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

namespace Sylius\Bundle\ThemeBundle\Translation\Resource;

final class TranslationResource implements TranslationResourceInterface
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $locale;

    /**
     * @var string
     */
    private $format;

    /**
     * @var string
     */
    private $domain;

    /**
     * @param string $filepath
     */
    public function __construct(string $filepath)
    {
        $this->name = $filepath;

        $parts = explode('.', basename($filepath), 3);
        if (3 !== count($parts)) {
            throw new \InvalidArgumentException(sprintf(
                'Could not create a translation resource with filepath "%s".',
                $filepath
            ));
        }

        $this->domain = $parts[0];
        $this->locale = $parts[1];
        $this->format = $parts[2];
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function getLocale(): string
    {
        return $this->locale;
    }

    /**
     * {@inheritdoc}
     */
    public function getFormat(): string
    {
        return $this->format;
    }

    /**
     * {@inheritdoc}
     */
    public function getDomain(): string
    {
        return $this->domain;
    }
}
