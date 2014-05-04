<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Sequence\Generator;

use Sylius\Component\Sequence\Model\SequenceSubjectInterface;
use Sylius\Component\Sequence\Repository\HashSubjectRepositoryInterface;

/**
 * Hash order number generator.
 *
 * @author Myke Hines <myke@webhines.com>
 */
class HashGenerator extends AbstractGenerator implements GeneratorInterface
{
    /**
     * This generates a 3 by 7 by 7 digit number (much like amazon's order identifier)
     * e.g. 105-3958356-3707476
     */
    const HASH_AMAZON = 'amazon';

    /**
     * @var HashSubjectRepositoryInterface
     */
    protected $subjectRepository;

    /**
     * @var string
     */
    protected $format;

    /**
     * @param HashSubjectRepositoryInterface $subjectRepository
     * @param string                         $format
     */
    public function __construct(HashSubjectRepositoryInterface $subjectRepository, $format = self::HASH_AMAZON)
    {
        $this->subjectRepository = $subjectRepository;
        $this->format            = $format;
    }

    /**
     * {@inheritdoc}
     */
    protected function generateNumber($index, SequenceSubjectInterface $subject)
    {
        do {
            switch ($this->format) {
                case self::HASH_AMAZON:
                    $number = $this->generateSegment(3) . '-' . $this->generateSegment(7) . '-' . $this->generateSegment(7);
                    break;

                default:
                    $number = $this->generateSegment(10);
            }
        } while ($this->subjectRepository->isNumberUsed($number));

        return $number;
    }

    /**
     * Generates a randomized segment
     *
     * @param int $length
     *
     * @return string Random characters
     */
    protected function generateSegment($length)
    {
        switch ($this->sequenceFormat) {
            case self::FORMAT_STRING:
                $input = $this->generateRandomString();
                break;

            case self::FORMAT_MIXED:
                $input = sha1(microtime(true));
                break;

            default:
                $input = mt_rand();
                break;
        }

        $segment = substr(str_pad($input, $length, 0, STR_PAD_LEFT), 0, $length);

        if (self::FORMAT_DIGITS !== $this->sequenceFormat) {
            if (self::CASE_UPPER === $this->formatCase) {
                return strtoupper($segment);
            }

            if (self::CASE_LOWER === $this->formatCase) {
                return strtolower($segment);
            }
        }

        return $segment;
    }

    private function generateRandomString($length = 10)
    {
        $characters   = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            // 51 = strlen($characters) - 1
            $randomString .= $characters[mt_rand(0, 51)];
        }

        return $randomString;
    }
}
