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
 * @author Joseph Bielawski <stloyd@gmail.com>
 */
class HashGenerator extends AbstractGenerator implements GeneratorInterface
{
    /**
     * @param HashSubjectRepositoryInterface $subjectRepository
     */
    public function __construct(HashSubjectRepositoryInterface $subjectRepository)
    {
        $this->subjectRepository = $subjectRepository;
    }

    /**
     * {@inheritdoc}
     */
    protected function generateNumber($index, SequenceSubjectInterface $subject)
    {
        do {
            $number = $this->generateSegment(10);
        } while ($this->subjectRepository->isNumberUsed($number));

        return $number;
    }

    /**
     * Generates a randomized segment.
     *
     * @param int $length
     *
     * @return string Random characters
     */
    protected function generateSegment($length)
    {
        switch ($this->sequenceFormat) {
            case GeneratorInterface::FORMAT_STRING:
                $input = $this->generateRandomString();
                break;

            case GeneratorInterface::FORMAT_MIXED:
                $input = sha1(microtime(true));
                break;

            default:
                $input = mt_rand();
                break;
        }

        $segment = substr(str_pad($input, $length, 0, STR_PAD_LEFT), 0, $length);

        if (GeneratorInterface::FORMAT_DIGITS !== $this->sequenceFormat) {
            if (GeneratorInterface::CASE_UPPER === $this->formatCase) {
                return strtoupper($segment);
            }

            if (GeneratorInterface::CASE_LOWER === $this->formatCase) {
                return strtolower($segment);
            }
        }

        return $segment;
    }

    /**
     * Generates a random string.
     *
     * @param int $length
     *
     * @return string
     */
    protected function generateRandomString($length = 10)
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
