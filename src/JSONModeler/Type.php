<?php namespace DCarbone\JSONModeler;

/*
 * Copyright (C) 2016-2018 Daniel Carbone (daniel.p.carbone@gmail.com)
 *
 * This software may be modified and distributed under the terms
 * of the MIT license.  See the LICENSE file for details.
 */

/**
 * Interface Type
 * @package DCarbone\JSONModeler
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
    public function example();

    /**
     * @return bool
     */
    public function isAlwaysDefined(): bool;

    /**
     * @return \DCarbone\JSONModeler\Type
     */
    public function notAlwaysDefined(): Type;

    /**
     * @return \DCarbone\JSONModeler\TypeParent|null
     */
    public function parent(): ?TypeParent;

    /**
     * @param \DCarbone\JSONModeler\TypeParent $parent
     * @return \DCarbone\JSONModeler\Type
     */
    public function setParent(TypeParent $parent): Type;
}