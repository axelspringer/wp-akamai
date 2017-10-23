<?php
/**
 *
 */

namespace Asse\Plugin\Akamai;

/**
 * Class EdgeControl
 *
 * @package Asse\Plugin\Akamai
 */
class EdgeControl
{
    /**
     * @var string
     */
    public $max_age = '1d';

    /**
     * @var string
     */
    public $downstream_ttl = '1m';

    /**
     * @var string
     */
    public $cache_option = '!no-store';

    /**
     * EdgeControl constructor.
     */
    public function __construct()
    {
    }

    /**
     * Builds and returns the Edge-Control header based on the current settings.
     *
     * @return string
     */
    public function build_header():string
    {
        return 'Edge-control: ' . $this->cache_option .
            ',max-age=' . $this->max_age .
            ',downstream-ttl=' . $this->downstream_ttl;
    }
}
