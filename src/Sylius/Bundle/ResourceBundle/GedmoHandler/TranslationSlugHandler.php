<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ResourceBundle\GedmoHandler;

use Doctrine\Common\Persistence\Mapping\ClassMetadata;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Gedmo\Exception\InvalidMappingException;
use Gedmo\Sluggable\Handler\SlugHandlerInterface;
use Gedmo\Sluggable\Mapping\Event\SluggableAdapter;
use Gedmo\Sluggable\SluggableListener;
use Gedmo\Tool\Wrapper\AbstractWrapper;

/**
 * This is the handler for the permalink fields that are in translation entities
 * is an adaptation of the default Gedmo Tree Handler.
 *
 * @author Gediminas Morkevicius <gediminas.morkevicius@gmail.com>
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
class TranslationSlugHandler implements SlugHandlerInterface
{
    const SEPARATOR = '/';

    /**
     * @var ObjectManager
     */
    protected $om;

    /**
     * @var SluggableListener
     */
    protected $sluggable;

    /**
     * @var string
     */
    private $prefix;

    /**
     * @var string
     */
    private $suffix;

    /**
     * True if node is being inserted
     *
     * @var bool
     */
    private $isInsert = false;

    /**
     * Transliterated parent slug
     *
     * @var string
     */
    private $parentSlug;

    /**
     * Used path separator
     *
     * @var string
     */
    private $usedPathSeparator;

    /**
     * {@inheritdoc}
     */
    public function __construct(SluggableListener $sluggable)
    {
        $this->sluggable = $sluggable;
    }

    /**
     * {@inheritdoc}
     */
    public function onChangeDecision(SluggableAdapter $ea, array &$config, $object, &$slug, &$needToChangeSlug)
    {
        $this->om = $ea->getObjectManager();
        $this->isInsert = $this->om->getUnitOfWork()->isScheduledForInsert($object);
        $options = $config['handlers'][get_called_class()];

        $this->usedPathSeparator = isset($options['separator']) ? $options['separator'] : self::SEPARATOR;
        $this->prefix = isset($options['prefix']) ? $options['prefix'] : '';
        $this->suffix = isset($options['suffix']) ? $options['suffix'] : '';

        if (!$this->isInsert && !$needToChangeSlug) {
            $changeSet = $ea->getObjectChangeSet($this->om->getUnitOfWork(), $object);
            if (isset($changeSet[$options['relationParentRelationField']])) {
                $needToChangeSlug = true;
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function postSlugBuild(SluggableAdapter $ea, array &$config, $object, &$slug)
    {
        $options = $config['handlers'][get_called_class()];
        $this->parentSlug = '';

        $wrapped = AbstractWrapper::wrap($object, $this->om);

        $relation = $wrapped->getPropertyValue($options['relationField']);
        $locale = $wrapped->getPropertyValue($options['locale']);

        $wrapped = AbstractWrapper::wrap($relation, $this->om);
        if ($parent = $wrapped->getPropertyValue($options['relationParentRelationField'])) {
            $translation = call_user_func_array([$parent, $options['translate']], [$locale]);

            $this->parentSlug = $translation->{$options['parentFieldMethod']}();

            // if needed, remove suffix from parentSlug, so we can use it to prepend it to our slug
            if (isset($options['suffix'])) {
                $suffix = $options['suffix'];

                if (substr($this->parentSlug, -strlen($suffix)) === $suffix) { //endsWith
                    $this->parentSlug = substr_replace($this->parentSlug, '', -1 * strlen($suffix));
                }
            }
        }

        $slug = $this->deleteUnnecessaryParentSlug($slug);
    }

    /**
     * {@inheritdoc}
     */
    public static function validate(array $options, ClassMetadata $meta)
    {
        // Since we cannot know, whether children of this mapped superclass
        // have or have not given association.
        if ($meta instanceof ClassMetadataInfo && $meta->isMappedSuperclass) {
            return;
        }

        if (!$meta->isSingleValuedAssociation($options['relationField'])) {
            throw new InvalidMappingException("Unable to find tree parent slug relation through field - [{$options['relationField']}] in class - {$meta->name}");
        }
//      TODO Check parent relation in translatable entity is single valued
//      (Note: don't know if that's possible here as we need the relationField class metadata)
    }

    /**
     * {@inheritdoc}
     */
    public function onSlugCompletion(SluggableAdapter $ea, array &$config, $object, &$slug)
    {
        $slug = $this->transliterate($slug);

        if (!$this->isInsert) {
            $wrapped = AbstractWrapper::wrap($object, $this->om);
            $meta = $wrapped->getMetadata();
            $target = $wrapped->getPropertyValue($config['slug']);
            $config['pathSeparator'] = $this->usedPathSeparator;
            $ea->replaceRelative($object, $config, $target.$config['pathSeparator'], $slug);
            $uow = $this->om->getUnitOfWork();
            // update in memory objects
            foreach ($uow->getIdentityMap() as $className => $objects) {
                // for inheritance mapped classes, only root is always in the identity map
                if ($className !== $wrapped->getRootObjectName()) {
                    continue;
                }
                foreach ($objects as $object) {
                    if (property_exists($object, '__isInitialized__') && !$object->__isInitialized__) {
                        continue;
                    }
                    $oid = spl_object_hash($object);
                    $objectSlug = $meta->getReflectionProperty($config['slug'])->getValue($object);
                    if (preg_match("@^{$target}{$config['pathSeparator']}@smi", $objectSlug)) {
                        $objectSlug = str_replace($target, $slug, $objectSlug);
                        $meta->getReflectionProperty($config['slug'])->setValue($object, $objectSlug);
                        $ea->setOriginalObjectProperty($uow, $oid, $config['slug'], $objectSlug);
                    }
                }
            }
        }
    }

    /**
     * Transliterates the slug and prefixes the slug
     * by collection of parent slugs.
     *
     * @param string $text
     *
     * @return string
     */
    public function transliterate($text)
    {
        if (!empty($this->parentSlug)) {
            return $this->parentSlug.$this->usedPathSeparator.$text.$this->suffix;
        }

        return $this->prefix.$text;
    }

    /**
     * {@inheritdoc}
     */
    public function handlesUrlization()
    {
        return false;
    }

    /**
     * @param string $slug
     *
     * @return string
     */
    private function deleteUnnecessaryParentSlug($slug)
    {
        return preg_replace('/^'.preg_quote($this->parentSlug.$this->usedPathSeparator, '/').'/', '', $slug);
    }
}
