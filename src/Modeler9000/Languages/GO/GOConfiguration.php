<?php

declare(strict_types=1);

namespace DCarbone\Modeler9000\Languages\GO;

/*
 * Copyright (C) 2016-2023 Daniel Carbone (daniel.p.carbone@gmail.com)
 *
 * This software may be modified and distributed under the terms
 * of the MIT license.  See the LICENSE file for details.
 */

use DCarbone\Modeler9000\Configuration;
use DCarbone\Modeler9000\Type;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * Class GOConfiguration
 * @package DCarbone\Modeler9000\Languages\GO
 */
class GOConfiguration implements Configuration
{
    use LoggerAwareTrait;

    public const KEY_ForceOmitEmpty = 'forceOmitEmpty';
    public const KEY_ForceIntToFloat = 'forceIntToFloat';
    public const KEY_UseSimpleInt = 'useSimpleInt';
    public const KEY_ForceScalarToPointer = 'forceScalarToPointer';
    public const KEY_ScalarPointersInSlices = 'scalarPointersInSlices';
    public const KEY_EmptyStructToInterface = 'emptyStructToInterface';
    public const KEY_BreakOutInlineStructs = 'breakOutInlineStructs';
    public const KEY_InitialNumberMap = 'initialNumberMap';
    public const KEY_SingleTypeBlock = 'singleTypeBlock';

    public const KEY_AdditionalTags = 'additionalTags';

    public const KEY_InvalidBeginningCharacterPrefix = 'invalidBeginningCharacterPrefix';

    /** @var array */
    protected static array $defaultValues = [
        self::KEY_ForceOmitEmpty => false,
        self::KEY_ForceIntToFloat => false,
        self::KEY_UseSimpleInt => false,
        self::KEY_ForceScalarToPointer => false,
        self::KEY_ScalarPointersInSlices => false,
        self::KEY_EmptyStructToInterface => false,
        self::KEY_BreakOutInlineStructs => true,
        self::KEY_SingleTypeBlock => false,
        self::KEY_AdditionalTags => [],
        self::KEY_InvalidBeginningCharacterPrefix => 'X',
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
    protected array $options = [];
    /** @var array */
    protected array $values = [];

    /**
     * GOConfiguration constructor.
     * @param array $config
     * @param null|\Psr\Log\LoggerInterface $logger
     */
    public function __construct(array $config = [], ?LoggerInterface $logger = null) {
        $this->setLogger($logger ?? new NullLogger());

        foreach (static::$defaultValues as $confKey => $defaultValue) {
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
    public function get(string $key): mixed {
        if (isset($this->options[$key])) {
            return $this->values[$key];
        }
        throw new \OutOfBoundsException("No key named \"{$key}\" found in " . get_class($this));
    }

    /**
     * @param string $key
     * @param mixed $value
     */
    public function set(string $key, mixed $value): void {
        if (isset($this->options[$key])) {
            if (gettype($value) === $this->options[$key]) {
                $this->values[$key] = $value;
            } else {
                throw new \InvalidArgumentException("Key \"{$key}\" must be of type \"{$this->options[$key]}\", \"" .
                    gettype($value) .
                    '" provided');
            }
        } else {
            throw new \OutOfBoundsException("No key named \"{$key}\" found in " . get_class($this));
        }
    }

    /**
     * @return \Psr\Log\LoggerInterface
     */
    public function logger(): LoggerInterface {
        return $this->logger;
    }

    /**
     * @param \DCarbone\Modeler9000\Languages\GO\Types\StructType $struct
     * @param \DCarbone\Modeler9000\Type $field
     * @return bool
     */
    public function isFieldIgnored(Types\StructType $struct, Type $field): bool {
        return false;
    }

    /**
     * @param \DCarbone\Modeler9000\Languages\GO\Types\StructType $struct
     * @param \DCarbone\Modeler9000\Type $field
     * @return string
     */
    public function buildFieldTag(Types\StructType $struct, Type $field): string {
        $tagDef = $this->_buildFieldTag($field, 'json', $field->isAlwaysDefined() || $this->get(self::KEY_ForceOmitEmpty) ? 'omitempty': '');

        foreach ($this->get(self::KEY_AdditionalTags) as $tagName) {
            $tagDef = sprintf('%s %s', $tagDef, $this->_buildFieldTag($field, $tagName));
        }

        return $tagDef;
    }

    /**
     * @param Type $field
     * @param string $tagName
     * @param string ...$extra
     * @return string
     */
    protected function _buildFieldTag(Type $field, string $tagName, string... $extra): string {
        $extra = array_filter($extra);
        $out = sprintf('%s:"%s', $tagName, $field->name());
        if ($extra !== []) {
            $out = sprintf('%s,%s', $out, implode(',', $extra));
        }
        return sprintf('%s"', $out);
    }
}