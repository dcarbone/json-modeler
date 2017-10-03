<?php namespace DCarbone\JSONModeler\Languages\GO;

/*
 * Copyright (C) 2016-2017 Daniel Carbone (daniel.p.carbone@gmail.com)
 *
 * This software may be modified and distributed under the terms
 * of the MIT license.  See the LICENSE file for details.
 */

use DCarbone\JSONModeler\Languages\GO\Types\MapType;
use DCarbone\JSONModeler\Languages\GO\Types\SliceType;
use DCarbone\JSONModeler\Namer;
use DCarbone\JSONModeler\Type;

/**
 * Class GONamer
 * @package DCarbone\JSONModeler\Languages\GO
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
    protected static $numberToWord = [
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

    /**
     * @param string $name
     * @return string
     */
    public function format(string $name): string {
        if (!$name) {
            return '';
        }

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
     * @param \DCarbone\JSONModeler\Type $type
     * @return string
     */
    public function goName(Type $type): string {
        if ($parent = ($type->parent())) {
            return $this->format($type->name());
        }
        return $type->name();
    }

    /**
     * @param \DCarbone\JSONModeler\Type $type
     * @return string
     */
    public function typeName(Type $type): string {
        if ($parent = $type->parent()) {
            if ($parent instanceof SliceType || $parent instanceof MapType) {
                return $this->typeName($parent);
            } else {
                return "{$this->typeName($parent)}{$this->goName($type)}";
            }
        } else {
            return $this->goName($type);
        }
    }

    /**
     * @param \DCarbone\JSONModeler\Type $type
     * @return string
     */
    public function typeMapName(Type $type): string {
        return "{$this->typeName($type)}Map";
    }

    /**
     * @param \DCarbone\JSONModeler\Type $type
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
        $ci = static::COMMON_INITIALISISMS;
        return preg_replace_callback('/(^|[^a-zA-Z])([a-z]+)/S',
            function ($item) use ($ci) {
                [$full, $symbol, $string] = $item;
                $upper = strtoupper($string);

                if (in_array($upper, $ci, true)) {
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
        $ci = static::COMMON_INITIALISISMS;
        return preg_replace_callback('/([A-Z])([a-z]+)/S',
            function ($item) use ($ci) {
                [$full] = $item;
                $upper = strtoupper($full);
                if (in_array($upper, $ci, true)) {
                    return $upper;
                }
                return $full;
            },
            $string);
    }
}