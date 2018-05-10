<?php namespace DCarbone\JSONModeler\Languages\GO\Types;

/*
 * Copyright (C) 2016-2018 Daniel Carbone (daniel.p.carbone@gmail.com)
 *
 * This software may be modified and distributed under the terms
 * of the MIT license.  See the LICENSE file for details.
 */

/**
 * Class SimpleType
 * @package DCarbone\JSONModeler\Languages\GO\Types
 */
class SimpleType extends AbstractType {
    /** @var string */
    protected $type;

    /**
     * SimpleType constructor.
     * @param string $name
     * @param $example
     * @param string $type
     */
    public function __construct(string $name, $example, string $type) {
        parent::__construct($name, $example);
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function type(): string {
        return $this->type;
    }
}