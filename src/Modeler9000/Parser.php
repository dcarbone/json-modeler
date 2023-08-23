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
 * Interface Parser
 * @package DCarbone\Modeler9000
 */
interface Parser {
    /**
     * @param string $name
     * @param mixed $example
     * @param \DCarbone\Modeler9000\TypeParent|null $parent
     * @return \DCarbone\Modeler9000\Type
     */
    public function parse(string $name, mixed $example, ?TypeParent $parent = null): Type;
}