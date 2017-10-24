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

namespace Sylius\Bundle\LocaleBundle\Templating\Helper;

use Sylius\Component\Locale\Converter\LocaleConverterInterface;
use Symfony\Component\Templating\Helper\Helper;

final class LocaleHelper extends Helper implements LocaleHelperInterface
{
    /**
     * @var LocaleConverterInterface
     */
    private $localeConverter;

    /**
     * @param LocaleConverterInterface $localeConverter
     */
    public function __construct(LocaleConverterInterface $localeConverter)
    {
        $this->localeConverter = $localeConverter;
    }

    /**
     * {@inheritdoc}
     */
    public function convertCodeToName(string $localeCode): ?string
    {
        return $this->localeConverter->convertCodeToName($localeCode);
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'sylius_locale';
    }
}
