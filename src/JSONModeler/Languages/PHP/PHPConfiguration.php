<?php namespace DCarbone\JSONModeler\Languages\PHP;

/*
 * Copyright (C) 2016-2018 Daniel Carbone (daniel.p.carbone@gmail.com)
 *
 * This software may be modified and distributed under the terms
 * of the MIT license.  See the LICENSE file for details.
 */

use DCarbone\JSONModeler\Configuration;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * Class PHPConfiguration
 * @package DCarbone\JSONModeler\Languages\PHP
 */
class PHPConfiguration implements Configuration {

    use LoggerAwareTrait;

    const KEY_DefaultZeroType = 'defaultZeroType';

    /** @var array */
    protected static $defaultValues = [
        self::KEY_DefaultZeroType => true,
    ];

    /** @var array */
    protected $options = [];
    /** @var array */
    protected $values = [];

    /**
     * PHPConfiguration constructor.
     * @param array $config
     * @param null|\Psr\Log\LoggerInterface $logger
     */
    public function __construct(array $config = [], ?LoggerInterface $logger = null) {
        $this->setLogger($logger ?? new NullLogger());

        foreach(static::$defaultValues as $confKey => $defaultValue) {
            $this->options[$confKey] = gettype($defaultValue);
            if (isset($config[$confKey])) {
                $this->set($confKey, $config[$confKey]);
            } else {
                $this->set($confKey, $defaultValue);
            }
        }
    }

    /**
     * @return array
     */
    public function options(): array {
        return $this->options;
    }

    /**
     * @param string $key
     * @return mixed
     */
    public function get(string $key) {
        if (isset($this->options[$key])) {
            return $this->values[$key];
        }
        throw new \OutOfBoundsException("No key named \"{$key}\" found in ".get_class($this));
    }

    /**
     * @param string $key
     * @param mixed $value
     */
    public function set(string $key, $value): void {
        if (isset($this->options[$key])) {
            if (gettype($value) === $this->options[$key]) {
                $this->values[$key] = $value;
            } else {
                throw new \InvalidArgumentException("Key \"{$key}\" must be of type \"{$this->options[$key]}\", \"".gettype($value).'" provided');
            }
        } else {
            throw new \OutOfBoundsException("No key named \"{$key}\" found in ".get_class($this));
        }
    }

    /**
     * @return \Psr\Log\LoggerInterface
     */
    public function logger(): LoggerInterface {
        return $this->logger;
    }
}