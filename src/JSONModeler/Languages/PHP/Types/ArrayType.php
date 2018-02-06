<?php namespace DCarbone\JSONModeler\Languages\PHP\Types;

/*
 * Copyright (C) 2016-2018 Daniel Carbone (daniel.p.carbone@gmail.com)
 *
 * This software may be modified and distributed under the terms
 * of the MIT license.  See the LICENSE file for details.
 */

use DCarbone\JSONModeler\Languages\PHP\PHPTyper;
use DCarbone\JSONModeler\Type;
use DCarbone\JSONModeler\TypeParent;

/**
 * Class ArrayType
 * @package DCarbone\JSONModeler\Languages\PHP\Types
 */
class ArrayType extends AbstractType implements TypeParent {
    /** @var \DCarbone\JSONModeler\Type */
    protected $arrayType = null;

    /**
     * @return array
     */
    public function __debugInfo() {
        return parent::__debugInfo() + ['arrayType' => $this->arrayType];
    }

    /**
     * @return string
     */
    public function type(): string {
        return PHPTyper::ARRAY;
    }

    /**
     * @param \DCarbone\JSONModeler\Type $type
     * @return \DCarbone\JSONModeler\Languages\PHP\Types\ArrayType
     */
    public function setArrayType(Type $type): ArrayType {
        $type->setParent($this);
        $this->arrayType = $type;
        return $this;
    }

    /**
     * @return \DCarbone\JSONModeler\Type
     */
    public function arrayType(): Type {
        return $this->arrayType;
    }

    /**
     * @return string
     */
    public function typehint(): string {
        return 'array';
    }

    /**
     * @return string
     */
    public function zero(): string {
        return '[]';
    }

    /**
     * @return string
     */
    public function cast(): string {
        return '';
    }
}