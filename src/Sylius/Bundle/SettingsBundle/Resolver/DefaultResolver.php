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
 * @author Steffen Brem <steffenbrem@gmail.com>
 */
class DefaultResolver implements SettingsResolverInterface
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
    public function resolve($schema)
    {
        try {
            return $this->settingRepository->findOneBy([
                'schema' => $schema
            ]);
        } catch (NonUniqueResultException $e) {
            throw new \LogicException(sprintf('Multiple schemas found for "%s". You should probably define a custom settings resolver for this schema.', $schema));
        }
    }
}
