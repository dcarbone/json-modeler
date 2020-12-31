<?php namespace DCarbone\JSONModeler;

/*
 * Copyright (C) 2016-2020 Daniel Carbone (daniel.p.carbone@gmail.com)
 *
 * This software may be modified and distributed under the terms
 * of the MIT license.  See the LICENSE file for details.
 */

/**
 * Interface Namer
 * @package DCarbone\JSONModeler
 */
interface Namer {
    /**
     * @param string $name
     * @return string
     */
    public function format(string $name): string;
}