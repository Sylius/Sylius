<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\InstallerBundle\Persister;

use Symfony\Component\Yaml\Yaml;
use RuntimeException;

class YamlPersister
{
    protected $file;

    public function __construct($file)
    {
        $this->file = $file;
    }

    public function parse()
    {
        $data = Yaml::parse($this->file);
        $parameters = array();

        foreach ($data['parameters'] as $key => $value) {
            $section = $this->getParametersSection($key);
            if (!isset($parameters[$section])) {
                $parameters[$section] = array();
            }
            $parameters[$section][str_replace('.', '_', $key)] = $value;
        }

        return $parameters;
    }

    public function dump(array $data)
    {
        $parameters = array();
        foreach ($data as $section) {
            foreach ($section as $key => $value) {
                $parameters[str_replace('_', '.', $key)] = $value;
            }
        }

        if (false === file_put_contents($this->file, Yaml::dump(array('parameters' => $parameters)))) {
            throw new RuntimeException(sprintf('Failed to write to %s.', $this->file));
        }
    }

    private function getParametersSection($key)
    {
        $parts = explode('.', $key);

        return 3 === count($parts) ? $parts[1] : 'locale';
    }
}
