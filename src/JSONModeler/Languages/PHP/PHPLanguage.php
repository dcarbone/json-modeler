<?php namespace DCarbone\JSONModeler\Languages\PHP;

/*
 * Copyright (C) 2016-2020 Daniel Carbone (daniel.p.carbone@gmail.com)
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
 * Class PHPLanguage
 * @package DCarbone\JSONModeler\Languages\PHP
 */
class PHPLanguage implements Language {
    const NAME = 'php';

    /** @var \DCarbone\JSONModeler\Languages\PHP\PHPConfiguration */
    protected $configuration;
    /** @var \DCarbone\JSONModeler\Languages\PHP\PHPNamer */
    protected $namer;
    /** @var \DCarbone\JSONModeler\Languages\PHP\PHPTyper */
    protected $typer;
    /** @var \DCarbone\JSONModeler\Languages\PHP\PHPParser */
    protected $parser;
    /** @var \DCarbone\JSONModeler\Languages\PHP\PHPWriter */
    protected $writer;

    /**
     * PHPLanguage constructor.
     * @param \DCarbone\JSONModeler\Languages\PHP\PHPConfiguration|null $configuration
     * @param \DCarbone\JSONModeler\Languages\PHP\PHPNamer|null $namer
     * @param \DCarbone\JSONModeler\Languages\PHP\PHPTyper|null $typer
     * @param \DCarbone\JSONModeler\Languages\PHP\PHPParser|null $parser
     * @param \DCarbone\JSONModeler\Languages\PHP\PHPWriter|null $writer
     */
    public function __construct(?PHPConfiguration $configuration = null,
                                ?PHPNamer $namer = null,
                                ?PHPTyper $typer = null,
                                ?PHPParser $parser = null,
                                ?PHPWriter $writer = null) {
        $this->configuration = $configuration ?? new PHPConfiguration();
        $this->namer = $namer ?? new PHPNamer();
        $this->typer = $typer ?? new PHPTyper();
        $this->parser = $parser ?? new PHPParser();
        $this->writer = $writer ?? new PHPWriter();
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