<?php namespace DCarbone\JSONModeler\Languages\PHP;

/*
 * Copyright (C) 2016-2020 Daniel Carbone (daniel.p.carbone@gmail.com)
 *
 * This software may be modified and distributed under the terms
 * of the MIT license.  See the LICENSE file for details.
 */

use DCarbone\JSONModeler\Namer;

/**
 * Class PHPNamer
 * @package DCarbone\JSONModeler\Languages\PHP
 */
class PHPNamer implements Namer {

    /** @var \DCarbone\JSONModeler\Languages\PHP\PHPLanguage */
    protected $language;

    /**
     * PHPNamer constructor.
     * @param \DCarbone\JSONModeler\Languages\PHP\PHPLanguage $language
     */
    public function __construct(PHPLanguage $language) {
        $this->language = $language;
    }

    /**
     * @param string $name
     * @return string
     */
    public function format(string $name): string {
        if (!$name) {
            return '';
        }

        if (preg_match('/^[^a-zA-Z_]/', $name)) {
            $name = "_{$name}";
        }

        return preg_replace('/[^a-zA-Z0-9_]/', '_', $name);
    }
}