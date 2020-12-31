<?php namespace DCarbone\JSONModeler\Languages\PHP\Types;

/*
 * Copyright (C) 2016-2020 Daniel Carbone (daniel.p.carbone@gmail.com)
 *
 * This software may be modified and distributed under the terms
 * of the MIT license.  See the LICENSE file for details.
 */

use DCarbone\JSONModeler\Languages\PHP\PHPTyper;
use DCarbone\JSONModeler\Type;
use DCarbone\JSONModeler\TypeParent;

/**
 * Class ObjectType
 * @package DCarbone\JSONModeler\Languages\PHP\Types
 */
class ObjectType extends AbstractType implements TypeParent {

    /** @var \DCarbone\JSONModeler\Type[] */
    protected $fields = [];

    /** @var string */
    protected $className;

    /**
     * @return array
     */
    public function __debugInfo() {
        return parent::__debugInfo() + ['fields' => $this->fields];
    }

    /**
     * @return string
     */
    public function type(): string {
        return PHPTyper::OBJECT;
    }

    /**
     * @return \DCarbone\JSONModeler\Type[]
     */
    public function fields(): array {
        return $this->fields;
    }

    /**
     * @param \DCarbone\JSONModeler\Type $child
     * @return \DCarbone\JSONModeler\Languages\PHP\Types\ObjectType
     */
    public function addChild(Type $child): ObjectType {
        $child->setParent($this);
        $this->fields[$child->name()] = $child;
        return $this;
    }

    /**
     * @return string
     */
    public function className(): string {
        return $this->className;
    }

    /**
     * @param string $name
     * @return \DCarbone\JSONModeler\Languages\PHP\Types\ObjectType
     */
    public function setClassName(string $name): ObjectType {
        $this->className = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function typehint(): string {
        return $this->className();
    }

    /**
     * @return string
     */
    public function zero(): string {
        return 'null';
    }

    /**
     * @return string
     */
    public function cast(): string {
        return '';
    }
}