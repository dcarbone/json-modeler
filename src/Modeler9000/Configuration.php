<?php

declare(strict_types=1);

namespace DCarbone\Modeler9000;

/*
 * Copyright (C) 2016-2023 Daniel Carbone (daniel.p.carbone@gmail.com)
 *
 * This software may be modified and distributed under the terms
 * of the MIT license.  See the LICENSE file for details.
 */

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;

/**
 * Interface Configuration
 * @package DCarbone\Modeler9000
 */
interface Configuration extends LoggerAwareInterface {
    /**
     * Must return associative array indicating available options in this config and the option value type.
     *
     * ex:
     *
     * ["forceOmitToEmpty" => "boolean"]
     *
     * @return array
     */
    public function options(): array;

    /**
     * Must return value for key, or fail in some way if key is invalid.
     *
     * @param string $key
     * @return mixed
     */
    public function get(string $key): mixed;

    /**
     * Must apply value to key, or fail in some way if key / valid is invalid.
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function set(string $key, mixed $value): void;

    /**
     * Must return a usable logger instance
     *
     * @return \Psr\Log\LoggerInterface
     */
    public function logger(): LoggerInterface;
}