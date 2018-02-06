<?php namespace DCarbone\JSONModeler\Languages\PHP;

/*
 * Copyright (C) 2016-2018 Daniel Carbone (daniel.p.carbone@gmail.com)
 *
 * This software may be modified and distributed under the terms
 * of the MIT license.  See the LICENSE file for details.
 */

use DCarbone\JSONModeler\TypeParent;
use DCarbone\JSONModeler\Typer;

/**
 * Class PHPTyper
 * @package DCarbone\JSONModeler\Languages\PHP
 */
class PHPTyper implements Typer {

    const MIXED = 'mixed';
    const INT = 'int';
    const FLOAT = 'double';
    const STRING = 'string';
    const BOOLEAN = 'bool';
    const ARRAY = 'array';
    const OBJECT = 'object';

    /** @var \DCarbone\JSONModeler\Languages\PHP\PHPLanguage */
    protected $language;

    /**
     * PHPTyper constructor.
     * @param \DCarbone\JSONModeler\Languages\PHP\PHPLanguage $language
     */
    public function __construct(PHPLanguage $language) {
        $this->language = $language;
    }

    public function type(string $name, $example, TypeParent $parent = null): string {
        // TODO: Implement type() method.
    }
}