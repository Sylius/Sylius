<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Metadata\HierarchyProvider;

use Sylius\Component\Metadata\Model\MetadataSubjectInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class CompositeMetadataHierarchyProvider implements MetadataHierarchyProviderInterface
{
    /**
     * @var MetadataHierarchyProviderInterface[]
     */
    protected $providers = [];

    /**
     * @param MetadataHierarchyProviderInterface[] $providers
     */
    public function __construct(array $providers = [])
    {
        foreach ($providers as $provider) {
            $this->addProvider($provider);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getHierarchyByMetadataSubject(MetadataSubjectInterface $metadata)
    {
        foreach ($this->providers as $provider) {
            if ($provider->supports($metadata)) {
                return $provider->getHierarchyByMetadataSubject($metadata);
            }
        }

        throw new \InvalidArgumentException(sprintf(
            'There is no provider suitable for %s',
            get_class($metadata)
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function supports(MetadataSubjectInterface $metadata)
    {
        foreach ($this->providers as $provider) {
            if ($provider->supports($metadata)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param MetadataHierarchyProviderInterface $provider
     */
    protected function addProvider(MetadataHierarchyProviderInterface $provider)
    {
        $this->providers[] = $provider;
    }
}
