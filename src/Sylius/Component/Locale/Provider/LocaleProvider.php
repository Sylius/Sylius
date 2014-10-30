<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Locale\Provider;

use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * Default provider returns all enabled locales.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class LocaleProvider implements LocaleProviderInterface
{
    /**
     * Repository for locale model.
     *
     * @var RepositoryInterface
     */
    protected $localeRepository;

    /**
     * @param RepositoryInterface $localeRepository
     */
    public function __construct(RepositoryInterface $localeRepository)
    {
        $this->localeRepository = $localeRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function getAvailableLocales()
    {
        return $this->localeRepository->findBy(array('enabled' => true));
    }
}
