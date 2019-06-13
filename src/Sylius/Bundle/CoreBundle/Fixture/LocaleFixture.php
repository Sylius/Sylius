<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\CoreBundle\Fixture;

use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Bundle\FixturesBundle\Fixture\AbstractFixture;
use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

class LocaleFixture extends AbstractFixture
{
    /** @var FactoryInterface */
    private $localeFactory;

    /** @var ObjectManager */
    private $localeManager;

    /** @var string */
    private $baseLocaleCode;

    /**
     * @param string $baseLocaleCode
     */
    public function __construct(FactoryInterface $localeFactory, ObjectManager $localeManager, $baseLocaleCode)
    {
        $this->localeFactory = $localeFactory;
        $this->localeManager = $localeManager;
        $this->baseLocaleCode = $baseLocaleCode;
    }

    /**
     * {@inheritdoc}
     */
    public function load(array $options): void
    {
        $localesCodes = $options['locales'];

        if ($options['load_default_locale'] && !in_array($this->baseLocaleCode, $localesCodes, true)) {
            $localesCodes = array_merge([$this->baseLocaleCode], $localesCodes);
        }

        foreach ($localesCodes as $localeCode) {
            /** @var LocaleInterface $locale */
            $locale = $this->localeFactory->createNew();

            $locale->setCode($localeCode);

            $this->localeManager->persist($locale);
        }

        $this->localeManager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'locale';
    }

    /**
     * {@inheritdoc}
     */
    protected function configureOptionsNode(ArrayNodeDefinition $optionsNode): void
    {
        $optionsNode
            ->children()
                ->scalarNode('load_default_locale')->defaultTrue()->end()
                ->arrayNode('locales')->scalarPrototype()->end()
        ;
    }
}
