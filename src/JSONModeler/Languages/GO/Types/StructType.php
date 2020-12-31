<?php namespace DCarbone\JSONModeler\Languages\GO\Types;

/*
 * Copyright (C) 2016-2020 Daniel Carbone (daniel.p.carbone@gmail.com)
 *
 * This software may be modified and distributed under the terms
 * of the MIT license.  See the LICENSE file for details.
 */

use DCarbone\JSONModeler\Languages\GO\GOTyper;
use DCarbone\JSONModeler\Type;
use DCarbone\JSONModeler\TypeParent;

/**
 * Class StructType
 * @package DCarbone\JSONModeler\Languages\GO\Types
 */
class StructType extends AbstractType implements TypeParent {
    /** @var \DCarbone\JSONModeler\Type[] */
    protected $fields = [];

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
        return GOTyper::STRUCT;
    }

    /**
     * @return \DCarbone\JSONModeler\Type[]
     */
    public function fields(): array {
        return $this->fields;
    }

    /**
     * @param \DCarbone\JSONModeler\Type $child
     * @return \DCarbone\JSONModeler\Languages\GO\Types\StructType
     */
    public function addChild(Type $child): StructType {
        $child->setParent($this);
        $this->fields[] = $child;
        return $this;
    }

}