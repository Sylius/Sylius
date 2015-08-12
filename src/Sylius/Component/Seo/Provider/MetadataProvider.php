<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Sylius\Component\Seo\Provider;

use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\Seo\Compiler\MetadataCompilerInterface;
use Sylius\Component\Seo\Model\MetadataInterface;
use Sylius\Component\Seo\Model\MetadataSubjectInterface;
use Sylius\Component\Seo\Model\RootMetadataInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class MetadataProvider implements MetadataProviderInterface
{
    /**
     * @var RepositoryInterface
     */
    protected $rootMetadataRepository;

    /**
     * @var MetadataCompilerInterface
     */
    protected $metadataCompiler;

    /**
     * @param RepositoryInterface $rootMetadataRepository
     * @param MetadataCompilerInterface $metadataCompiler
     */
    public function __construct(RepositoryInterface $rootMetadataRepository, MetadataCompilerInterface $metadataCompiler)
    {
        $this->rootMetadataRepository = $rootMetadataRepository;
        $this->metadataCompiler = $metadataCompiler;
    }

    /**
     * {@inheritdoc}
     */
    public function getMetadataBySubject(MetadataSubjectInterface $metadataSubject)
    {
        return $this->getMetadataByKey($metadataSubject->getMetadataIdentifier());
    }

    /**
     * @param string $key
     *
     * @return MetadataInterface
     *
     * @throws \InvalidArgumentException If root metadata with given key was not found
     */
    protected function getMetadataByKey($key)
    {
        /** @var RootMetadataInterface $rootMetadata */
        $rootMetadata = $this->rootMetadataRepository->findOneBy(['key' => $key]);

        if (null === $rootMetadata) {
            throw new \InvalidArgumentException(sprintf('Root metadata with key "%s" was not found!', $key));
        }

        return $this->metadataCompiler->compile($rootMetadata);
    }
}