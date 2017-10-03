<?php namespace DCarbone\JSONModeler\Languages\GO;

/*
 * Copyright (C) 2016-2017 Daniel Carbone (daniel.p.carbone@gmail.com)
 *
 * This software may be modified and distributed under the terms
 * of the MIT license.  See the LICENSE file for details.
 */

use DCarbone\JSONModeler\Language;
use DCarbone\JSONModeler\Languages\GO\Types\InterfaceType;
use DCarbone\JSONModeler\Languages\GO\Types\MapType;
use DCarbone\JSONModeler\Languages\GO\Types\SliceType;
use DCarbone\JSONModeler\Type;
use DCarbone\JSONModeler\TypeParent;
use DCarbone\JSONModeler\Typer;

/**
 * Class GOTyper
 * @package DCarbone\JSONModeler\Languages\GO
 */
class GOTyper implements Typer {

    const INT = 'int';
    const INT32 = 'int32';
    const INT64 = 'int64';
    const FLOAT32 = 'float32';
    const FLOAT64 = 'float64';
    const STRING = 'string';
    const BOOLEAN = 'bool';
    const SLICE = 'slice';
    const STRUCT = 'struct';
    const MAP = 'map';
    const INTERFACE = 'interface{}';
    const RAWMESSAGE = 'raw';

    /** @var \DCarbone\JSONModeler\Language */
    protected $language;

    /**
     * GOTyper constructor.
     * @param \DCarbone\JSONModeler\Language $language
     */
    public function __construct(Language $language) {
        $this->language = $language;
    }

    /**
     * @param string $name
     * @param mixed $example
     * @param \DCarbone\JSONModeler\TypeParent|null $parent
     * @return string
     */
    public function type(string $name, $example, TypeParent $parent = null): string {
        $type = gettype($example);

        if ('string' === $type) {
            return self::STRING;
        }

        if ('integer' === $type) {
            if ($this->language->configuration()->get(GOConfiguration::KEY_ForceIntToFloat)) {
                return self::FLOAT64;
            }

            if ($this->language->configuration()->get(GOConfiguration::KEY_UseSimpleInt)) {
                return self::INT;
            }

            if ($example > -2147483648 && $example < 2147483647) {
                return self::INT;
            }

            return self::INT64;
        }

        if ('boolean' === $type) {
            return self::BOOLEAN;
        }

        if ('double' === $type) {
            return self::FLOAT64;
        }

        if ('array' === $type) {
            return self::SLICE;
        }

        if ('object' === $type) {
            return self::STRUCT;
        }

        return self::INTERFACE;
    }

    /**
     * @param \DCarbone\JSONModeler\Type $type
     * @return bool
     */
    public function isSimpleGoType(Type $type) {
        return $type instanceof Types\SimpleType;
    }

    /**
     * @param \DCarbone\JSONModeler\Type $type1
     * @param \DCarbone\JSONModeler\Type $type2
     * @return \DCarbone\JSONModeler\Type
     */
    public function mostSpecificPossibleSimpleGoType(Type $type1, Type $type2): Type {
        if (get_class($type1) === get_class($type2)) {
            return $type1;
        }

        if ('float' === substr($type1->type(), 0, 5) && 'int' === substr($type2->type(), 0, 3)) {
            return $type1;
        }

        if ('int' === substr($type1->type(), 0, 3) && 'float' === substr($type2->type(), 0, 5)) {
            return $type1;
        }

        return new Types\InterfaceType($type1->name(), $type1->example());
    }

    /**
     * @param \DCarbone\JSONModeler\Type $type1
     * @param \DCarbone\JSONModeler\Type $type2
     * @return \DCarbone\JSONModeler\Type
     */
    public function mostSpecificPossibleComplexGoType(Type $type1, Type $type2): Type {
        if ($type1 instanceof SliceType && $type2 instanceof SliceType) {
            $compType = $this->mostSpecificPossibleGoType($type1->sliceType(), $type2->sliceType());
            if (!($compType instanceof Types\InterfaceType)) {
                return $type1;
            }
        } else if ($type1 instanceof Types\MapType && $type2 instanceof MapType) {
            $compType = $this->mostSpecificPossibleGoType($type1->mapType(), $type2->mapType());
            if (!($compType instanceof InterfaceType)) {
                return $type1;
            }
        } else if ($type1 instanceof Types\StructType && $type2 instanceof Types\StructType) {
            // TODO: This is a terrible assumption...
            return new Types\StructType(
                $type1->name(),
                json_decode(json_encode(get_object_vars($type1->example()) + get_object_vars($type2->example())))
            );
        } else if (get_class($type1) === get_class($type2)) {
            return $type1;
        } else if ($type1 instanceof Types\InterfaceType && !($type2 instanceof InterfaceType)) {
            return $type2;
        } else if (!($type1 instanceof Types\InterfaceType) && $type2 instanceof Types\InterfaceType) {
            return $type1;
        }

        return new Types\InterfaceType($type1->name(), $type1->example());
    }

    /**
     * @param \DCarbone\JSONModeler\Type $type1
     * @param \DCarbone\JSONModeler\Type $type2
     * @return \DCarbone\JSONModeler\Type
     */
    public function mostSpecificPossibleGoType(Type $type1, Type $type2): Type {
        if ($this->isSimpleGoType($type1) && $this->isSimpleGoType($type2)) {
            return $this->mostSpecificPossibleSimpleGoType($type1, $type2);
        }

        return $this->mostSpecificPossibleComplexGoType($type1, $type2);
    }
}