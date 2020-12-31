<?php namespace DCarbone\JSONModeler;

/*
 * Copyright (C) 2016-2020 Daniel Carbone (daniel.p.carbone@gmail.com)
 *
 * This software may be modified and distributed under the terms
 * of the MIT license.  See the LICENSE file for details.
 */

/**
 * Interface Parser
 * @package DCarbone\JSONModeler
 */
interface Parser {
    /**
     * @param string $name
     * @param mixed $example
     * @param \DCarbone\JSONModeler\TypeParent|null $parent
     * @return \DCarbone\JSONModeler\Type
     */
    public function parse(string $name, $example, ?TypeParent $parent = null): Type;
}