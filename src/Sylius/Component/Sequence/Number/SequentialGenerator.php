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
class SequentialGenerator extends AbstractGenerator implements GeneratorInterface
{
    /**
     * Order number max length.
     *
     * @var integer
     */
    protected $numberLength;

    /**
     * Start number
     *
     * @var integer
     */
    protected $startNumber;

    /**
     * Constructor.
     *
     * @param SequenceManagerInterface $manager
     * @param integer                  $numberLength
     * @param integer                  $startNumber
     */
    public function __construct(SequenceManagerInterface $manager, $numberLength = 9, $startNumber = 1)
    {
        parent::__construct($manager);

        $this->numberLength = $numberLength;
        $this->startNumber  = $startNumber;
    }

    /**
     * {@inheritdoc}
     */
    protected function generateNumber($index, SequenceSubjectInterface $subject)
    {
        $number = $this->startNumber + $index;

        return str_pad($number, $this->numberLength, 0, STR_PAD_LEFT);
    }
}
