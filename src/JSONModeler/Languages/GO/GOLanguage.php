<?php namespace DCarbone\JSONModeler\Languages\GO;

/*
 * Copyright (C) 2016-2023 Daniel Carbone (daniel.p.carbone@gmail.com)
 *
 * This software may be modified and distributed under the terms
 * of the MIT license.  See the LICENSE file for details.
 */

use DCarbone\JSONModeler\Configuration;
use DCarbone\JSONModeler\Language;
use DCarbone\JSONModeler\Namer;
use DCarbone\JSONModeler\Parser;
use DCarbone\JSONModeler\Typer;
use DCarbone\JSONModeler\Writer;

/**
 * Class GOLanguage
 * @package DCarbone\JSONModeler\Languages\GO
 */
class GOLanguage implements Language {
    const NAME = 'golang';

    /** @var \DCarbone\JSONModeler\Languages\GO\GOConfiguration */
    protected GOConfiguration $configuration;
    /** @var \DCarbone\JSONModeler\Languages\GO\GONamer */
    protected GONamer $namer;
    /** @var \DCarbone\JSONModeler\Languages\GO\GOTyper */
    protected GOTyper $typer;
    /** @var \DCarbone\JSONModeler\Languages\GO\GOParser */
    protected GOParser $parser;
    /** @var \DCarbone\JSONModeler\Languages\GO\GOWriter */
    protected GOWriter $writer;

    /**
     * GOLanguage constructor.
     * @param \DCarbone\JSONModeler\Languages\GO\GOConfiguration|null $configuration
     * @param \DCarbone\JSONModeler\Languages\GO\GONamer|null $namer
     * @param \DCarbone\JSONModeler\Languages\GO\GOTyper|null $typer
     * @param \DCarbone\JSONModeler\Languages\GO\GOParser|null $parser
     * @param \DCarbone\JSONModeler\Languages\GO\GOWriter|null $writer
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
     * @return \DCarbone\JSONModeler\Configuration
     */
    public function configuration(): Configuration {
        return $this->configuration;
    }

    /**
     * @return \DCarbone\JSONModeler\Namer
     */
    public function namer(): Namer {
        return $this->namer;
    }

    /**
     * @return \DCarbone\JSONModeler\Parser
     */
    public function parser(): Parser {
        return $this->parser;
    }

    /**
     * @return \DCarbone\JSONModeler\Typer
     */
    public function typer(): Typer {
        return $this->typer;
    }

    /**
     * @return \DCarbone\JSONModeler\Writer
     */
    public function writer(): Writer {
        return $this->writer;
    }
}