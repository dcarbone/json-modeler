<?php

declare(strict_types=1);

namespace DCarbone\Modeler9000\Languages\GO;

/*
 * Copyright (C) 2016-2023 Daniel Carbone (daniel.p.carbone@gmail.com)
 *
 * This software may be modified and distributed under the terms
 * of the MIT license.  See the LICENSE file for details.
 */

use DCarbone\Modeler9000\Namer;
use DCarbone\Modeler9000\Type;

/**
 * Class GONamer
 * @package DCarbone\Modeler9000\Languages\GO
 */
class GONamer implements Namer {

    const COMMON_INITIALISISMS = [
        'API',
        'ASCII',
        'CPU',
        'CSS',
        'DNS',
        'EOF',
        'GUID',
        'HTML',
        'HTTP',
        'HTTPS',
        'ID',
        'IP',
        'JSON',
        'LHS',
        'QPS',
        'RAM',
        'RHS',
        'RPC',
        'SLA',
        'SMTP',
        'SSH',
        'TCP',
        'TLS',
        'TTL',
        'UDP',
        'UI',
        'UID',
        'UUID',
        'URI',
        'URL',
        'UTF8',
        'VM',
        'XML',
        'XSRF',
        'XSS',
    ];

    /**
     * Map of integer => valid starting character
     * @var array
     */
    protected static array $numberToWord = [
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
    ];

    /** @var \DCarbone\Modeler9000\Languages\GO\GOLanguage */
    protected GOLanguage $language;

    /**
     * GONamer constructor.
     * @param \DCarbone\Modeler9000\Languages\GO\GOLanguage $language
     */
    public function __construct(GOLanguage $language) {
        $this->language = $language;
    }

    /**
     * @param string $name
     * @return string
     */
    public function format(string $name): string {
        // If entire name is a number...
        if (preg_match('/^\d+$/S', $name)) {
            $name = sprintf('Num%s', $name);
        } // If first character of name is a number...
        else if (preg_match('/^\d/S', $name)) {
            $name = static::$numberToWord[substr($name, 0, 1)] . substr($name, 1);
        }

        // If this starts with anything other than an alpha character prefix with X
        if (preg_match('/^[^a-zA-Z]/S', $name)) {
            $name = sprintf('X%s', $name);
        }

        // Do stuff with non-alphanumeric characters
        $name = $this->special($name);

        // Case it and remove non-alpha characters
        $name = $this->case($name);

        // Finally, strip out everything that was not caught above and is not an alphanumeric character.
        return preg_replace('/[^a-zA-Z0-9]/S', '', $name);
    }

    /**
     * @param \DCarbone\Modeler9000\Type $type
     * @return string
     */
    public function goName(Type $type): string {
        if ($parent = ($type->parent())) {
            return $this->format($type->name());
        }
        return $type->name();
    }

    /**
     * @param \DCarbone\Modeler9000\Type $type
     * @return string
     */
    public function typeName(Type $type): string {
        if ($parent = $type->parent()) {
            if ($parent instanceof Types\SliceType || $parent instanceof Types\MapType) {
                return $this->typeName($parent);
            } else {
                return "{$this->typeName($parent)}{$this->goName($type)}";
            }
        } else {
            return $this->goName($type);
        }
    }

    /**
     * @param \DCarbone\Modeler9000\Type $type
     * @return string
     */
    public function typeMapName(Type $type): string {
        return "{$this->typeName($type)}Map";
    }

    /**
     * @param \DCarbone\Modeler9000\Type $type
     * @return string
     */
    public function typeSliceName(Type $type): string {
        return "{$this->typeName($type)}Slice";
    }

    /**
     * @param string $string
     * @return string
     */
    protected function special(string $string): string {
        return preg_replace_callback('/(^|[^a-zA-Z])([a-z]+)/S',
            function ($item) {
                [$full, $symbol, $string] = $item;
                $upper = strtoupper($string);

                if (in_array($upper, static::COMMON_INITIALISISMS, true)) {
                    return $upper;
                }
                return ucfirst(strtolower($string));
            },
            $string);
    }

    /**
     * @param string $string
     * @return string
     */
    protected function case(string $string): string {
        return preg_replace_callback('/([A-Z])([a-z]+)/S',
            function ($item) {
                [$full] = $item;
                $upper = strtoupper($full);
                if (in_array($upper, static::COMMON_INITIALISISMS, true)) {
                    return $upper;
                }
                return $full;
            },
            $string);
    }
}