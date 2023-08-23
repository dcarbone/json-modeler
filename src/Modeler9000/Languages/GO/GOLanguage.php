<?php

declare(strict_types=1);

namespace DCarbone\Modeler9000\Languages\GO;

/*
 * Copyright (C) 2016-2023 Daniel Carbone (daniel.p.carbone@gmail.com)
 *
 * This software may be modified and distributed under the terms
 * of the MIT license.  See the LICENSE file for details.
 */

use DCarbone\Modeler9000\Configuration;
use DCarbone\Modeler9000\Language;
use DCarbone\Modeler9000\Namer;
use DCarbone\Modeler9000\Parser;
use DCarbone\Modeler9000\Typer;
use DCarbone\Modeler9000\Writer;

/**
 * Class GOLanguage
 * @package DCarbone\Modeler9000\Languages\GO
 */
class GOLanguage implements Language {
    const NAME = 'golang';

    /** @var \DCarbone\Modeler9000\Languages\GO\GOConfiguration */
    protected GOConfiguration $configuration;
    /** @var \DCarbone\Modeler9000\Languages\GO\GONamer */
    protected GONamer $namer;
    /** @var \DCarbone\Modeler9000\Languages\GO\GOTyper */
    protected GOTyper $typer;
    /** @var \DCarbone\Modeler9000\Languages\GO\GOParser */
    protected GOParser $parser;
    /** @var \DCarbone\Modeler9000\Languages\GO\GOWriter */
    protected GOWriter $writer;

    /**
     * GOLanguage constructor.
     * @param \DCarbone\Modeler9000\Languages\GO\GOConfiguration|null $configuration
     * @param \DCarbone\Modeler9000\Languages\GO\GONamer|null $namer
     * @param \DCarbone\Modeler9000\Languages\GO\GOTyper|null $typer
     * @param \DCarbone\Modeler9000\Languages\GO\GOParser|null $parser
     * @param \DCarbone\Modeler9000\Languages\GO\GOWriter|null $writer
     */
    public function __construct(?GOConfiguration $configuration = null,
                                ?GONamer $namer = null,
                                ?GOTyper $typer = null,
                                ?GOParser $parser = null,
                                ?GOWriter $writer = null) {
        $this->configuration = $configuration ?? new GOConfiguration();
        $this->namer = $writer ?? new GONamer($this);
        $this->typer = $typer ?? new GOTyper($this);
        $this->parser = $parser ?? new GOParser($this);
        $this->writer = $writer ?? new GOWriter($this);
    }

    /**
     * @return string
     */
    public function name(): string {
        return self::NAME;
    }

    /**
     * @return \DCarbone\Modeler9000\Configuration
     */
    public function configuration(): Configuration {
        return $this->configuration;
    }

    /**
     * @return \DCarbone\Modeler9000\Namer
     */
    public function namer(): Namer {
        return $this->namer;
    }

    /**
     * @return \DCarbone\Modeler9000\Parser
     */
    public function parser(): Parser {
        return $this->parser;
    }

    /**
     * @return \DCarbone\Modeler9000\Typer
     */
    public function typer(): Typer {
        return $this->typer;
    }

    /**
     * @return \DCarbone\Modeler9000\Writer
     */
    public function writer(): Writer {
        return $this->writer;
    }
}