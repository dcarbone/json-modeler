<?php namespace DCarbone\JSONModeler;

/*
 * Copyright (C) 2016-2020 Daniel Carbone (daniel.p.carbone@gmail.com)
 *
 * This software may be modified and distributed under the terms
 * of the MIT license.  See the LICENSE file for details.
 */

/**
 * Interface Language
 * @package DCarbone\JSONModeler
 */
interface Language {
    /**
     * @return string
     */
    public function name(): string;

    /**
     * @return \DCarbone\JSONModeler\Configuration
     */
    public function configuration(): Configuration;

    /**
     * @return \DCarbone\JSONModeler\Namer
     */
    public function namer(): Namer;

    /**
     * @return \DCarbone\JSONModeler\Parser
     */
    public function parser(): Parser;

    /**
     * @return \DCarbone\JSONModeler\Typer
     */
    public function typer(): Typer;

    /**
     * @return \DCarbone\JSONModeler\Writer
     */
    public function writer(): Writer;
}