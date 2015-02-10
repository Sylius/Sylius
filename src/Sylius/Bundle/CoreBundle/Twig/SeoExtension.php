<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Twig;

use Sylius\Bundle\SettingsBundle\Model\Settings;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Storage\StorageInterface;

class SeoExtension extends \Twig_Extension
{
    /**
     * @var StorageInterface
     */
    private $storage;

    /**
     * @var Settings
     */
    private $settings;

    public function __construct(StorageInterface $storage, Settings $settings)
    {
        $this->storage  = $storage;
        $this->settings = $settings;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('sylius_seo_meta_title', array($this, 'getTitle'), array('pre_escape' => 'html', 'is_safe' => array('html'))),
            new \Twig_SimpleFunction('sylius_seo_meta_keywords', array($this, 'getMetaKeywords'), array('pre_escape' => 'html', 'is_safe' => array('html'))),
            new \Twig_SimpleFunction('sylius_seo_meta_description', array($this, 'getMetaDescription'), array('pre_escape' => 'html', 'is_safe' => array('html'))),
        );
    }

    public function getTitle($resource = null)
    {
        if (null === $resource) {
            return $this->settings->get('global_title_formula');
        }

        $data = $this->storage->getData($this->generateCacheKey($resource));

        return $data['title'];
    }

    public function getMetaKeywords($resource = null)
    {
        if (null === $resource) {
            return $this->settings->get('global_meta_keywords_formula');
        }

        $data = $this->storage->getData($this->generateCacheKey($resource));

        return $data['keywords'];
    }

    public function getMetaDescription($resource = null)
    {
        if (null === $resource) {
            return $this->settings->get('global_meta_description_formula');
        }

        $data = $this->storage->getData($this->generateCacheKey($resource));

        return $data['desc'];
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sylius_seo';
    }

    private function generateCacheKey($resource)
    {
        return sprintf('%s_%s', get_class($resource), $resource->getId());
    }
}
