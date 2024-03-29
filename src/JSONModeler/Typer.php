<?php namespace DCarbone\JSONModeler;

/*
 * Copyright (C) 2016-2023 Daniel Carbone (daniel.p.carbone@gmail.com)
 *
 * This software may be modified and distributed under the terms
 * of the MIT license.  See the LICENSE file for details.
 */

/**
 * Interface Typer
 * @package DCarbone\JSONModeler
 */
interface Typer {
    /**
     * @param string $name
     * @param mixed $example
     * @param \DCarbone\JSONModeler\TypeParent|null $parent
     * @return string
     */
    public function type(string $name, mixed $example, TypeParent $parent = null): string;
}