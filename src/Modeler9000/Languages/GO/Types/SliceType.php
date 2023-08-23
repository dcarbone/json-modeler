<?php

declare(strict_types=1);

namespace DCarbone\Modeler9000\Languages\GO\Types;

/*
 * Copyright (C) 2016-2023 Daniel Carbone (daniel.p.carbone@gmail.com)
 *
 * This software may be modified and distributed under the terms
 * of the MIT license.  See the LICENSE file for details.
 */

use DCarbone\Modeler9000\Languages\GO\GOTyper;
use DCarbone\Modeler9000\Type;
use DCarbone\Modeler9000\TypeParent;

/**
 * Class SliceType
 * @package DCarbone\Modeler9000\Languages\GO\Types
 */
class SliceType extends AbstractType implements TypeParent {
    /** @var \DCarbone\Modeler9000\Type|null */
    protected ?Type $sliceType = null;

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
     * @param \DCarbone\Modeler9000\Type $type
     * @return \DCarbone\Modeler9000\Languages\GO\Types\SliceType
     */
    public function setSliceType(Type $type): SliceType {
        $type->setParent($this);
        $this->sliceType = $type;
        return $this;
    }

    /**
     * @return \DCarbone\Modeler9000\Type
     */
    public function sliceType(): Type {
        return $this->sliceType;
    }
}