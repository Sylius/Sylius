<?php

namespace Sylius\Bundle\CoreBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

class DateIntervalTransformer implements DataTransformerInterface
{
    /**
     * @var array
     */
    protected $formOptions;

    public function __construct(array $formOptions)
    {
        if (!array_key_exists('units', $formOptions) ||
            !is_array($formOptions['units'])
        ) {
            throw new \InvalidArgumentException(
                'DateIntervalTransformer invalid $formOptions argument.'
            );
        }

        $this->formOptions = $formOptions;
    }

    /**
     * {@inheritdoc}
     */
    public function transform($value)
    {
        if (null === $value || !$value instanceof \DateInterval) {
            return array('frequency' => null, 'unit' => null);
        }

        $frequency = null;
        $unit = null;
        foreach ($this->formOptions['units'] as $choice => $label) {
            if ($value->$choice === 0) {
                continue;
            }

            if (!$frequency || $value->$choice < $frequency) {
                $frequency = $value->$choice;
                $unit = $choice;
            }
        }

        return array('frequency' => $frequency, 'unit' => $unit);
    }

    /**
     * {@inheritdoc}
     */
    public function reverseTransform($value)
    {
        if (null === $value['frequency'] || null === $value['unit']) {
            return null;
        }

        try {
            return new \DateInterval(
                "P" . $value['frequency'] . strtoupper($value['unit'])
            );
        } catch (\Exception $e) {
            // invalid interval
            return null;
        }
    }
}
