<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Currency\Importer;

class OpenExchangeRatesImporter extends AbstractImporter
{
    /**
     * @var string
     */
    private $url = 'http://openexchangerates.org/api/currencies.json';

    /**
     * {@inheritdoc}
     */
    public function configure(array $options = [])
    {
        if (!isset($options['app_id'])) {
            throw new \InvalidArgumentException('"OER_APP_ID" must be set in order to use OERImporter.');
        }

        $this->url .= '?app_id='.$options['app_id'];
    }

    /**
     * {@inheritdoc}
     */
    public function import(array $managedCurrencies = [])
    {
        $data = @file_get_contents($this->url);
        $data = @json_decode($data, true);
        if (is_array($data) && isset($data['rates'])) {
            $data = $data['rates'];
            foreach ($data as $code => $rate) {
                $this->updateOrCreate($managedCurrencies, $code, (float) $rate);
            }

            $this->manager->flush();
        }
    }
}
