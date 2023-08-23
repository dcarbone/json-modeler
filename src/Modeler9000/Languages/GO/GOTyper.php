<?php

declare(strict_types=1);

namespace DCarbone\Modeler9000\Languages\GO;

/*
 * Copyright (C) 2016-2023 Daniel Carbone (daniel.p.carbone@gmail.com)
 *
 * This software may be modified and distributed under the terms
 * of the MIT license.  See the LICENSE file for details.
 */

use DCarbone\Modeler9000\TypeParent;
use DCarbone\Modeler9000\Typer;

/**
 * Class GOTyper
 * @package DCarbone\Modeler9000\Languages\GO
 */
class GOTyper implements Typer {

    const INT = 'int';
    const INT32 = 'int32';
    const INT64 = 'int64';
    const FLOAT32 = 'float32';
    const FLOAT64 = 'float64';
    const STRING = 'string';
    const BOOLEAN = 'bool';
    const SLICE = 'slice';
    const STRUCT = 'struct';
    const MAP = 'map';
    const INTERFACE = 'interface{}';
    const RAWMESSAGE = 'raw';

    /** @var \DCarbone\Modeler9000\Languages\GO\GOLanguage */
    protected GOLanguage $language;

    /**
     * GOTyper constructor.
     * @param \DCarbone\Modeler9000\Languages\GO\GOLanguage $language
     */
    public function __construct(GOLanguage $language) {
        $this->language = $language;
    }

    /**
     * @param string $name
     * @param mixed $example
     * @param \DCarbone\Modeler9000\TypeParent|null $parent
     * @return string
     */
    public function type(string $name, mixed $example, TypeParent $parent = null): string {
        $type = gettype($example);

        if ('string' === $type) {
            return self::STRING;
        }

        if ('integer' === $type) {
            if ($this->language->configuration()->get(GOConfiguration::KEY_ForceIntToFloat)) {
                return self::FLOAT64;
            }

            if ($this->language->configuration()->get(GOConfiguration::KEY_UseSimpleInt)) {
                return self::INT;
            }

            if ($example > -2147483648 && $example < 2147483647) {
                return self::INT;
            }

            return self::INT64;
        }

        if ('boolean' === $type) {
            return self::BOOLEAN;
        }

        if ('double' === $type) {
            return self::FLOAT64;
        }

        if ('array' === $type) {
            return self::SLICE;
        }

        if ('object' === $type) {
            return self::STRUCT;
        }

        return self::INTERFACE;
    }
}