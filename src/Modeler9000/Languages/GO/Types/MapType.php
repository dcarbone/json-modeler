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
 * Class MapType
 * @package DCarbone\Modeler9000\Languages\GO\Types
 */
class MapType extends AbstractType implements TypeParent {
    /** @var Type */
    protected Type $mapType;

    /**
     * @return array
     */
    public function __debugInfo() {
        return parent::__debugInfo() + ['mapType' => $this->mapType()];
    }

    /**
     * @return string
     */
    public function type(): string {
        return GOTyper::MAP;
    }

    /**
     * @param \DCarbone\Modeler9000\Type $mapType
     * @return \DCarbone\Modeler9000\Languages\GO\Types\MapType
     */
    public function setMapType(Type $mapType): MapType {
        $mapType->setParent($this);
        $this->mapType = $mapType;
        return $this;
    }

    /**
     * @return \DCarbone\Modeler9000\Type
     */
    public function mapType(): Type {
        return $this->mapType;
    }
}