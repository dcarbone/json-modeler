<?php

declare(strict_types=1);

namespace DCarbone\Modeler9000\Languages\GO\Types;

/*
 * Copyright (C) 2016-2023 Daniel Carbone (daniel.p.carbone@gmail.com)
 *
 * This software may be modified and distributed under the terms
 * of the MIT license.  See the LICENSE file for details.
 */

use DCarbone\Modeler9000\Languages\GO\GOTyper;

/**
 * Class InterfaceType
 * @package DCarbone\Modeler9000\Languages\GO\Types
 */
class InterfaceType extends AbstractType {
    /**
     * @return string
     */
    public function type(): string {
        return GOTyper::INTERFACE;
    }
}