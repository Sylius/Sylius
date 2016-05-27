<?php
/**
 * @author    Pete Ward <peter.ward@reiss.com>
 * @date      27/05/2016
 * @copyright Copyright (c) Reiss Clothing Ltd.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Metadata;

use Doctrine\Common\Cache\Cache;
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Sylius\Component\Metadata\Accessor\MetadataAccessorInterface;
use Sylius\Component\Metadata\Model\MetadataSubjectInterface;

/**
 * @author Pete Ward <peter.ward@reiss.com>
 */
class CachedMetadataAccessor implements MetadataAccessorInterface
{
    const DEFAULT_TTL = 300;
    const CACHE_KEY_PREFIX = 'metadata:';

    /**
     * @var LocaleContextInterface
     */
    private $localeContext;

    /**
     * @var Cache
     */
    private $cache;

    /**
     * @var MetadataAccessorInterface
     */
    private $metadataAccessor;

    /**
     * @var int
     */
    private $ttl;

    /**
     * @param MetadataAccessorInterface $metadataAccessor
     * @param LocaleContextInterface    $localeContext
     * @param Cache                     $cache
     * @param int                       $ttl
     */
    public function __construct(
        MetadataAccessorInterface $metadataAccessor,
        LocaleContextInterface $localeContext,
        Cache $cache,
        $ttl = self::DEFAULT_TTL
    ) {
        $this->metadataAccessor = $metadataAccessor;
        $this->localeContext = $localeContext;
        $this->cache = $cache;
        $this->ttl = $ttl;
    }

    /**
     * {@inheritdoc}
     */
    public function getProperty(MetadataSubjectInterface $metadataSubject, $type, $propertyPath = null)
    {
        $cacheKey = $this->getCacheKey($metadataSubject, $type, $propertyPath);

        if ($this->cache->contains($cacheKey)) {
            return $this->cache->fetch($cacheKey);
        }

        $metadata = $this->metadataAccessor->getProperty($metadataSubject, $type, $propertyPath);

        $this->cache->save($cacheKey, $metadata, $this->ttl);

        return $metadata;
    }

    /**
     * @param MetadataSubjectInterface $metadataSubject
     * @param string $type
     * @param string|null $propertyPath
     *
     * @return string
     */
    private function getCacheKey(MetadataSubjectInterface $metadataSubject, $type, $propertyPath)
    {
        return self::CACHE_KEY_PREFIX .
            $type .
            ':' .
            $metadataSubject->getMetadataIdentifier() .
            ':' .
            $this->localeContext->getCurrentLocale() .
            ($propertyPath ? ':' . $propertyPath : '')
        ;
    }
}