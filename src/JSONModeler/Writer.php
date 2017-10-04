<?php namespace DCarbone\JSONModeler;

/*
 * Copyright (C) 2016-2017 Daniel Carbone (daniel.p.carbone@gmail.com)
 *
 * This software may be modified and distributed under the terms
 * of the MIT license.  See the LICENSE file for details.
 */

/**
 * Interface Writer
 * @package DCarbone\JSONModeler
 */
interface Writer {
    /**
     * @param \DCarbone\JSONModeler\Type $type
     * @param int $indentLevel
     * @return string
     */
    public function write(Type $type, int $indentLevel = 0): string;
}