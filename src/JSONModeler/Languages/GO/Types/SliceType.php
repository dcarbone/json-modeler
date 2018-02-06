<?php namespace DCarbone\JSONModeler\Languages\GO\Types;

/*
 * Copyright (C) 2016-2018 Daniel Carbone (daniel.p.carbone@gmail.com)
 *
 * This software may be modified and distributed under the terms
 * of the MIT license.  See the LICENSE file for details.
 */

use DCarbone\JSONModeler\Languages\GO\GOTyper;
use DCarbone\JSONModeler\Type;
use DCarbone\JSONModeler\TypeParent;

/**
 * Class SliceType
 * @package DCarbone\JSONModeler\Languages\GO\Types
 */
class SliceType extends AbstractType implements TypeParent {
    /** @var \DCarbone\JSONModeler\Type */
    protected $sliceType = null;

    /**
     * @return array
     */
    public function __debugInfo() {
        return parent::__debugInfo() + ['sliceType' => $this->sliceType];
    }

    /**
     * @return string
     */
    public function type(): string {
        return GOTyper::SLICE;
    }

    /**
     * @param \DCarbone\JSONModeler\Type $type
     * @return \DCarbone\JSONModeler\Languages\GO\Types\SliceType
     */
    public function setSliceType(Type $type): SliceType {
        $type->setParent($this);
        $this->sliceType = $type;
        return $this;
    }

    /**
     * @return \DCarbone\JSONModeler\Type
     */
    public function sliceType(): Type {
        return $this->sliceType;
    }
}