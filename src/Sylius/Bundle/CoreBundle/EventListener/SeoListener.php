<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\EventListener;

use Sylius\Bundle\SettingsBundle\Model\Settings;
use Sylius\Component\Resource\Event\ResourceEvent;
use Sylius\Component\Resource\Model\SeoExtraInterface;
use Sylius\Component\Resource\Model\SeoMetaInterface;
use Sylius\Component\Storage\StorageInterface;

class SeoListener
{
    /**
     * @var StorageInterface
     */
    private $storage;

    /**
     * @var Settings
     */
    private $settings;

    private $formulas = array();

    public function __construct(StorageInterface $storage, Settings $settings, array $formulas)
    {
        $this->storage  = $storage;
        $this->settings = $settings;
        $this->formulas = $formulas;
    }

    public function preShow(ResourceEvent $event)
    {
        $resource = $event->getSubject();
        if ($resource instanceof SeoMetaInterface || $resource instanceof SeoExtraInterface) {
            $name = $this->generateCacheKey($resource);
            #if (!$this->storage->hasData($name)) {
                $this->storage->setData($name, $this->getData($resource));
            #}
        }
    }

    public function postUpdate(ResourceEvent $event)
    {
        $resource = $event->getSubject();
        if ($resource instanceof SeoMetaInterface || $resource instanceof SeoExtraInterface) {
            $this->storage->setData($this->generateCacheKey($resource), $this->getData($resource));
        }
    }

    public function preDelete(ResourceEvent $event)
    {
        $resource = $event->getSubject();
        if ($resource instanceof SeoMetaInterface || $resource instanceof SeoExtraInterface) {
            $this->storage->removeData($this->generateCacheKey($resource));
        }
    }

    private function generateCacheKey($resource)
    {
        return sprintf('%s_%s', get_class($resource), $resource->getId());
    }

    private function getData(SeoMetaInterface $resource)
    {
        $data = array();
        if ($template = $resource->getMetaTitle()) {
            $data['title'] = $template;
        } else {
            $data['title'] = $this->fetchData($resource, 'global_title_formula', 'title');
        }

        if ($template = $resource->getMetaKeywords()) {
            $data['keywords'] = $template;
        } else {
            $data['keywords'] = $this->fetchData($resource, 'global_meta_keywords_formula', 'keywords');
        }

        if ($template = $resource->getMetaDescription()) {
            $data['desc'] = $template;
        } else {
            $data['desc'] = $this->fetchData($resource, 'global_meta_description_formula', 'desc');
        }

        if ($resource instanceof SeoExtraInterface && $template = $resource->getSeoExtra()) {
            $data['extra'] = $template;
        }

        return $data;
    }

    private function fetchData($resource, $formulaName, $formulaType)
    {
        $template = null;
        $classes  = array(get_class($resource) => get_class($resource)) + class_implements($resource) + class_parents($resource);
        foreach ($this->formulas as $class => $formula) {
            if (isset($classes[$class])) {
                $template = $formula[$formulaType];

                break;
            }
        }

        return $this->render($template ?: $this->settings->get($formulaName), 'template.html', array('resource' => $resource));
    }

    private function render($template, $templateName, array $data)
    {
        $twig = new \Twig_Environment(new \Twig_Loader_Array(array(
            $templateName => $template,
        )), array('autoescape' => false));

        return $twig->render($templateName, $data);
    }
}
