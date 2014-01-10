<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\WebBundle\Behat;

use Behat\Behat\Event\FeatureEvent;
use Behat\Behat\Formatter\ProgressFormatter;
use Behat\Gherkin\Node\AbstractNode;
use Behat\Gherkin\Node\FeatureNode;


/**
 * Sylius Behat Formatter for Travis.
 *
 * @author Julien Janvier <j.janvier@gmail.com>
 */
class SyliusFormatter extends ProgressFormatter
{
    public static function getSubscribedEvents()
    {
        $events = array('beforeFeature', 'afterSuite', 'afterStep');

        return array_combine($events, $events);
    }

    /**
     * Listens to "feature.before" event.
     *
     * @param FeatureEvent $event
     *
     * @uses printFeatureHeader()
     */
    public function beforeFeature(FeatureEvent $event)
    {
        $this->printFeatureHeader($event->getFeature());
    }

    /**
     * Prints feature header.
     *
     * @param FeatureNode $feature
     *
     * @uses printFeatureOrScenarioTags()
     * @uses printFeatureName()
     * @uses printFeatureDescription()
     */
    protected function printFeatureHeader(FeatureNode $feature)
    {
        $this->writeln();
        $this->printFeatureOrScenarioTags($feature);
        $this->printFeatureName($feature);
        $this->writeln();
    }

    /**
     * Prints node tags.
     *
     * @param AbstractNode $node
     */
    protected function printFeatureOrScenarioTags(AbstractNode $node)
    {
        if (count($tags = $node->getOwnTags())) {
            $tags = implode(' ', array_map(function($tag){
                        return '@' . $tag;
                    }, $tags));


            $this->writeln("{+tag}$tags{-tag}");
        }
    }

    /**
     * Prints feature keyword and name.
     *
     * @param FeatureNode $feature
     *
     * @uses getFeatureOrScenarioName()
     */
    protected function printFeatureName(FeatureNode $feature)
    {
        $this->writeln("{+tag}" . $this->getFeatureOrScenarioName($feature) . "{-tag}");
    }

    /**
     * Returns feature or scenario name.
     *
     * @param AbstractNode $node
     * @param Boolean      $haveBaseIndent
     *
     * @return string
     */
    protected function getFeatureOrScenarioName(AbstractNode $node, $haveBaseIndent = true)
    {
        $keyword    = $node->getKeyword();
        $baseIndent = ($node instanceof FeatureNode) || !$haveBaseIndent ? '' : '  ';

        $lines = explode("\n", $node->getTitle());
        $title = array_shift($lines);

        if (count($lines)) {
            foreach ($lines as $line) {
                $title .= "\n" . $baseIndent.'  '.$line;
            }
        }

        return "$baseIndent$keyword:" . ($title ? ' ' . $title : '');
    }

}