<?php

declare(strict_types=1);

namespace DCarbone\Modeler9000;

/*
 * Copyright (C) 2016-2023 Daniel Carbone (daniel.p.carbone@gmail.com)
 *
 * This software may be modified and distributed under the terms
 * of the MIT license.  See the LICENSE file for details.
 */

/**
 * Interface Type
 * @package DCarbone\Modeler9000
 */
interface Type {
    /**
     * @return string
     */
    public function type(): string;

    /**
     * @return string
     */
    public function name(): string;

    /**
     * @return mixed
     */
    public function example(): mixed;

    /**
     * @return bool
     */
    public function isAlwaysDefined(): bool;

    /**
     * @return \DCarbone\Modeler9000\Type
     */
    public function notAlwaysDefined(): Type;

    /**
     * @return \DCarbone\Modeler9000\TypeParent|null
     */
    public function parent(): ?TypeParent;

    /**
     * @param \DCarbone\Modeler9000\TypeParent $parent
     * @return \DCarbone\Modeler9000\Type
     */
    public function setParent(TypeParent $parent): Type;
}