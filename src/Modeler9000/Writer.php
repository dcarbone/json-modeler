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
 * Interface Writer
 * @package DCarbone\Modeler9000
 */
interface Writer {
    /**
     * @param \DCarbone\Modeler9000\Type $type
     * @param int $indentLevel
     * @return string
     */
    public function write(Type $type, int $indentLevel = 0): string;
}