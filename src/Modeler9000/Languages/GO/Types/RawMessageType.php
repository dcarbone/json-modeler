<?php

declare(strict_types=1);

namespace DCarbone\Modeler9000\Languages\GO\Types;

/*
 * Copyright (C) 2016-2023 Daniel Carbone (daniel.p.carbone@gmail.com)
 *
 * This software may be modified and distributed under the terms
 * of the MIT license.  See the LICENSE file for details.
 */

/**
 * Class RawMessageType
 * @package DCarbone\Modeler9000\Types
 */
class RawMessageType extends SimpleType {
    /**
     * RawMessageType constructor.
     * @param string $name
     * @param mixed $example
     */
    public function __construct(string $name, $example) {
        parent::__construct($name, $example, 'json.RawMessage');
    }
}