<?php namespace DCarbone\JSONModeler\Languages\PHP\Types;

/*
 * Copyright (C) 2016-2018 Daniel Carbone (daniel.p.carbone@gmail.com)
 *
 * This software may be modified and distributed under the terms
 * of the MIT license.  See the LICENSE file for details.
 */

use DCarbone\JSONModeler\Languages\PHP\PHPTyper;
use DCarbone\JSONModeler\Type;

/**
 * Class MixedType
 * @package DCarbone\JSONModeler\Languages\PHP\Types
 */
class MixedType extends AbstractType implements Type {
    /**
     * @return string
     */
    public function type(): string {
        return PHPTyper::MIXED;
    }

    /**
     * @return string
     */
    public function typehint(): string {
        return '';
    }

    /**
     * @return string
     */
    public function zero(): string {
        return 'null';
    }

    /**
     * @return string
     */
    public function cast(): string {
        return '';
    }
}