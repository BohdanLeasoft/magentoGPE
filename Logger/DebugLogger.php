<?php
/**
 * All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace GingerPay\Payment\Logger;

use Monolog\Logger;
use Magento\Framework\Serialize\Serializer\Json;

/**
 * Logger Class for Debug Logger
 */
class DebugLogger extends Logger
{

    /**
     * @var Json
     */
    private $json;

    /**
     * DebugLogger constructor.
     * @param Json $json
     * @param string $name
     * @param array $handlers
     * @param array $processors
     */
    public function __construct(
        Json $json,
        string $name,
        array $handlers = [],
        array $processors = []
    ) {
        $this->json = $json;
        parent::__construct($name, $handlers, $processors);
    }

    /**
     * Add debug data to Log
     *
     * @param string $type
     * @param mixed $data
     *
     * @return void
     */
    public function addLog(string $type, $data)
    {
        if (is_array($data) || is_object($data)) {
            $this->addRecord(static::DEBUG, (string)$type . ': ' . $this->json->serialize($data), []);
        } else {
            $this->addRecord(static::DEBUG, (string)$type . ': ' . $data, []);
        }
    }
}
