<?php

declare(strict_types=1);

namespace DCarbone\Modeler9000\Languages\GO\Types;

/*
 * Copyright (C) 2016-2023 Daniel Carbone (daniel.p.carbone@gmail.com)
 *
 * This software may be modified and distributed under the terms
 * of the MIT license.  See the LICENSE file for details.
 */

use DCarbone\Modeler9000\Type;
use DCarbone\Modeler9000\TypeParent;

/**
 * Class AbstractType
 * @package DCarbone\Modeler9000\Languages\GO\Types
 */
abstract class AbstractType implements Type {

    /** @var string */
    protected string $name;
    /** @var mixed */
    protected mixed $example;

    /** @var bool */
    protected bool $alwaysDefined = true;

    /** @var \DCarbone\Modeler9000\TypeParent|null */
    protected TypeParent|null $parent = null;

    /**
     * AbstractType constructor.
     * @param string $name
     * @param $example
     * @param \DCarbone\Modeler9000\TypeParent|null $parent
     */
    public function __construct(string $name, $example, ?TypeParent $parent = null) {
        $this->name = $name;
        $this->example = $example;
        $this->parent = $parent;
    }

    /**
     * @return array
     */
    public function __debugInfo() {
        return [
            'name' => $this->name,
            'alwaysDefined' => $this->alwaysDefined,
            'example' => $this->example,
        ];
    }

    /**
     * @return string
     */
    public function name(): string {
        return $this->name;
    }

    /**
     * @return mixed
     */
    public function example(): mixed {
        return $this->example;
    }

    /**
     * @return bool
     */
    public function isAlwaysDefined(): bool {
        return $this->alwaysDefined;
    }

    /**
     * @return \DCarbone\Modeler9000\Type
     */
    public function notAlwaysDefined(): Type {
        $this->alwaysDefined = false;
        return $this;
    }

    /**
     * @return \DCarbone\Modeler9000\TypeParent|null
     */
    public function parent(): ?TypeParent {
        return $this->parent;
    }

    /**
     * @param \DCarbone\Modeler9000\TypeParent $parent
     * @return \DCarbone\Modeler9000\Type
     */
    public function setParent(TypeParent $parent): Type {
        $this->parent = $parent;
        return $this;
    }
}