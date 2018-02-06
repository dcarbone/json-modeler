<?php namespace DCarbone\JSONModeler\Languages\PHP;

/*
 * Copyright (C) 2016-2018 Daniel Carbone (daniel.p.carbone@gmail.com)
 *
 * This software may be modified and distributed under the terms
 * of the MIT license.  See the LICENSE file for details.
 */

use DCarbone\JSONModeler\Parser;
use DCarbone\JSONModeler\Type;
use DCarbone\JSONModeler\TypeParent;

/**
 * Class PHPParser
 * @package DCarbone\JSONModeler\Languages\PHP
 */
class PHPParser implements Parser {

    /** @var \DCarbone\JSONModeler\Languages\PHP\PHPLanguage */
    protected $language;

    /**
     * PHPParser constructor.
     * @param \DCarbone\JSONModeler\Languages\PHP\PHPLanguage $language
     */
    public function __construct(PHPLanguage $language) {
        $this->language = $language;
    }

    /**
     * @param string $name
     * @param mixed $example
     * @param \DCarbone\JSONModeler\TypeParent|null $parent
     * @return \DCarbone\JSONModeler\Type
     */
    public function parse(string $name, $example, ?TypeParent $parent = null): Type {
        // TODO: Implement parse() method.
    }
}