<?php namespace DCarbone\JSONModeler\Languages\GO;

/*
 * Copyright (C) 2016-2017 Daniel Carbone (daniel.p.carbone@gmail.com)
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
    protected $configuration;
    /** @var \DCarbone\JSONModeler\Languages\GO\GONamer */
    protected $namer;
    /** @var \DCarbone\JSONModeler\Languages\GO\GOTyper */
    protected $typer;
    /** @var \DCarbone\JSONModeler\Languages\GO\GOParser  */
    protected $parser;
    /** @var \DCarbone\JSONModeler\Languages\GO\GOWriter */
    protected $writer;

    /**
     * GOLanguage constructor.
     * @param \DCarbone\JSONModeler\Languages\GO\GOConfiguration $configuration
     */
    public function __construct(GOConfiguration $configuration = null) {
        if (!$configuration) {
            $configuration = new GOConfiguration();
        }
        $this->configuration = $configuration;
        $this->namer = new GONamer($this);
        $this->typer = new GOTyper($this);
        $this->parser = new GOParser($this);
        $this->writer = new GOWriter($this);
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