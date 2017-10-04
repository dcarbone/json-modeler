<?php namespace DCarbone\JSONModeler\Languages\GO;

/*
 * Copyright (C) 2016-2017 Daniel Carbone (daniel.p.carbone@gmail.com)
 *
 * This software may be modified and distributed under the terms
 * of the MIT license.  See the LICENSE file for details.
 */

use DCarbone\JSONModeler\Configuration;
use DCarbone\JSONModeler\Languages\GO\Types\StructType;
use DCarbone\JSONModeler\Type;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * Class GOConfiguration
 * @package DCarbone\JSONModeler\Languages\GO
 */
class GOConfiguration implements Configuration {

    use LoggerAwareTrait;

    const KEY_ForceOmitEmpty = 'forceOmitEmpty';
    const KEY_ForceIntToFloat = 'forceIntToFloat';
    const KEY_UseSimpleInt = 'useSimpleInt';
    const KEY_ForceScalarToPointer = 'forceScalarToPointer';
    const KEY_EmptyStructToInterface = 'emptyStructToInterface';
    const KEY_BreakOutInlineStructs = 'breakOutInlineStructs';
    const KEY_InitialNumberMap = 'initialNumberMap';

    /** @var array */
    protected static $defaultValues = [
        self::KEY_ForceOmitEmpty => false,
        self::KEY_ForceIntToFloat => false,
        self::KEY_UseSimpleInt => false,
        self::KEY_ForceScalarToPointer => false,
        self::KEY_EmptyStructToInterface => false,
        self::KEY_BreakOutInlineStructs => true,
        self::KEY_InitialNumberMap => [
            'Zero_',
            'One_',
            'Two_',
            'Three_',
            'Four_',
            'Five_',
            'Six_',
            'Seven_',
            'Eight_',
            'Nine_',
        ],
    ];

    /** @var array */
    protected $options = [];
    /** @var array */
    protected $values = [];

    /**
     * GOConfiguration constructor.
     * @param array $config
     * @param null|\Psr\Log\LoggerInterface $logger
     */
    public function __construct(array $config = [], ?LoggerInterface $logger = null) {
        if (!$logger) {
            $logger = new NullLogger();
        }
        $this->setLogger($logger);

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

    /**
     * @param \DCarbone\JSONModeler\Languages\GO\Types\StructType $struct
     * @param \DCarbone\JSONModeler\Type $field
     * @return bool
     */
    public function isFieldExported(StructType $struct, Type $field): bool {
        return true;
    }

    /**
     * @param \DCarbone\JSONModeler\Languages\GO\Types\StructType $struct
     * @param \DCarbone\JSONModeler\Type $field
     * @return bool
     */
    public function isFieldIgnored(Types\StructType $struct, Type $field): bool {
        return false;
    }

    /**
     * @param \DCarbone\JSONModeler\Languages\GO\Types\StructType $struct
     * @param \DCarbone\JSONModeler\Type $field
     * @return string
     */
    public function buildFieldTag(Types\StructType $struct, Type $field): string {
        $tag = sprintf('json:"%s', $field->name());

        if (!$field->isAlwaysDefined() || $this->get(self::KEY_ForceOmitEmpty)) {
            $tag = sprintf('%s,omitempty', $tag);
        }

        return sprintf('%s"', $tag);
    }
}