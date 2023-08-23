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
 * Interface Language
 * @package DCarbone\Modeler9000
 */
interface Language {
    /**
     * @return string
     */
    public function name(): string;

    /**
     * @return \DCarbone\Modeler9000\Configuration
     */
    public function configuration(): Configuration;

    /**
     * @return \DCarbone\Modeler9000\Namer
     */
    public function namer(): Namer;

    /**
     * @return \DCarbone\Modeler9000\Parser
     */
    public function parser(): Parser;

    /**
     * @return \DCarbone\Modeler9000\Typer
     */
    public function typer(): Typer;

    /**
     * @return \DCarbone\Modeler9000\Writer
     */
    public function writer(): Writer;
}