<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Sequence\Number;

use Sylius\Component\Sequence\Model\SequenceSubjectInterface;
use Sylius\Component\Sequence\Manager\SequenceManagerInterface;

/**
 * Default order number generator.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 * @author Saša Stamenković <umpirsky@gmail.com>
 * @author Alexandre Bacco <alexandre.bacco@gmail.com>
 */
abstract class AbstractGenerator implements GeneratorInterface
{
    /**
     * Sequence manager.
     *
     * @var SequenceManagerInterface
     */
    protected $manager;

    /**
     * Constructor.
     *
     * @param SequenceManagerInterface $manager
     */
    public function __construct(SequenceManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    /**
     * {@inheritdoc}
     */
    public function generate(SequenceSubjectInterface $subject)
    {
        if (null !== $subject->getNumber()) {
            return;
        }

        $subject->setNumber($this->generateNumber($this->manager->setNextIndex($subject->getSequenceType()), $subject));
    }

    /**
     * Generate the number
     *
     * @param int                      $index
     * @param SequenceSubjectInterface $subject
     * @return string
     */
    abstract protected function generateNumber($index, SequenceSubjectInterface $subject);
}
