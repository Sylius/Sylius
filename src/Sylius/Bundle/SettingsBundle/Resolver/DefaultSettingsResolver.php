<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\SettingsBundle\Resolver;

use Doctrine\ORM\NonUniqueResultException;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * Default implementation that resolves settings in the simplest way possible. It tries to find a schema by it's name.
 *
 * @author Steffen Brem <steffenbrem@gmail.com>
 */
class DefaultSettingsResolver implements SettingsResolverInterface
{
    /**
     * @var RepositoryInterface
     */
    protected $settingRepository;

    public function __construct(RepositoryInterface $settingRepository)
    {
        $this->settingRepository = $settingRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function resolve($schemaAlias)
    {
        try {
            return $this->settingRepository->findOneBy([
                'schemaAlias' => $schemaAlias
            ]);
        } catch (NonUniqueResultException $e) {
            throw new \LogicException(sprintf('Multiple schemas found for "%s". You should probably define a custom settings resolver for this schema.', $schemaAlias));
        }
    }
}
