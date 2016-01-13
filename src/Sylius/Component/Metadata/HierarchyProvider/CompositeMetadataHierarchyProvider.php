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
final class CompositeMetadataHierarchyProvider implements MetadataHierarchyProviderInterface
{
    /**
     * @var MetadataHierarchyProviderInterface[]
     */
    private $providers;

    /**
     * @param MetadataHierarchyProviderInterface[] $providers
     */
    public function __construct(array $providers = [])
    {
        $this->assertProvidersHaveCorrectType($providers);

        $this->providers = $providers;
    }

    /**
     * {@inheritdoc}
     */
    public function getHierarchyByMetadataSubject(MetadataSubjectInterface $metadataSubject)
    {
        foreach ($this->providers as $provider) {
            if ($provider->supports($metadataSubject)) {
                return $provider->getHierarchyByMetadataSubject($metadataSubject);
            }
        }

        return [
            $metadataSubject->getMetadataIdentifier(),
            $metadataSubject->getMetadataClassIdentifier(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function supports(MetadataSubjectInterface $metadataSubject)
    {
        return true;
    }

    /**
     * @param MetadataHierarchyProviderInterface[] $providers
     */
    private function assertProvidersHaveCorrectType(array $providers)
    {
        foreach ($providers as $provider) {
            if ($provider instanceof MetadataHierarchyProviderInterface) {
                continue;
            }

            throw new \InvalidArgumentException(sprintf(
                'Metadata hierarchy provider should have type "%s"',
                MetadataHierarchyProviderInterface::class
            ));
        }
    }
}
