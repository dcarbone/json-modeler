<?php namespace DCarbone\JSONModeler\Languages\GO\Types;

/*
 * Copyright (C) 2016-2023 Daniel Carbone (daniel.p.carbone@gmail.com)
 *
 * This software may be modified and distributed under the terms
 * of the MIT license.  See the LICENSE file for details.
 */

use DCarbone\JSONModeler\Type;
use DCarbone\JSONModeler\TypeParent;

/**
 * Class AbstractType
 * @package DCarbone\JSONModeler\Languages\GO\Types
 */
abstract class AbstractType implements Type {

    /** @var string */
    protected $name;
    /** @var mixed */
    protected $example;

    /** @var bool */
    protected $alwaysDefined = true;

    /** @var \DCarbone\JSONModeler\TypeParent|null */
    protected $parent = null;

    /**
     * AbstractType constructor.
     * @param string $name
     * @param $example
     * @param \DCarbone\JSONModeler\TypeParent|null $parent
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
     * @return \DCarbone\JSONModeler\Type
     */
    public function notAlwaysDefined(): Type {
        $this->alwaysDefined = false;
        return $this;
    }

    /**
     * @return \DCarbone\JSONModeler\TypeParent|null
     */
    public function parent(): ?TypeParent {
        return $this->parent;
    }

    /**
     * @param \DCarbone\JSONModeler\TypeParent $parent
     * @return \DCarbone\JSONModeler\Type
     */
    public function setParent(TypeParent $parent): Type {
        $this->parent = $parent;
        return $this;
    }
}