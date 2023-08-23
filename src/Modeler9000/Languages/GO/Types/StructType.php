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
 * Class StructType
 * @package DCarbone\Modeler9000\Languages\GO\Types
 */
class StructType extends AbstractType implements TypeParent {
    /** @var \DCarbone\Modeler9000\Type[] */
    protected array $fields = [];

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
     * @return \DCarbone\Modeler9000\Type[]
     */
    public function fields(): array {
        return $this->fields;
    }

    /**
     * @param \DCarbone\Modeler9000\Type $child
     * @return \DCarbone\Modeler9000\Languages\GO\Types\StructType
     */
    public function addChild(Type $child): StructType {
        $child->setParent($this);
        $this->fields[] = $child;
        return $this;
    }

}