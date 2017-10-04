<?php namespace DCarbone\JSONModeler\Languages\GO;

/*
 * Copyright (C) 2016-2017 Daniel Carbone (daniel.p.carbone@gmail.com)
 *
 * This software may be modified and distributed under the terms
 * of the MIT license.  See the LICENSE file for details.
 */

use DCarbone\JSONModeler\Language;
use DCarbone\JSONModeler\Parser;
use DCarbone\JSONModeler\Type;
use DCarbone\JSONModeler\TypeParent;

/**
 * Class GOParser
 * @package DCarbone\JSONModeler\Languages\GO
 */
class GOParser implements Parser {
    /** @var \DCarbone\JSONModeler\Language */
    protected $language;

    /**
     * GOParser constructor.
     * @param \DCarbone\JSONModeler\Language $language
     */
    public function __construct(Language $language) {
        $this->language = $language;
    }

    /**
     * @param string $name
     * @param $example
     * @param \DCarbone\JSONModeler\TypeParent|null $parent
     * @return \DCarbone\JSONModeler\Type
     */
    public function parse(string $name, $example, ?TypeParent $parent = null): Type {
        $goType = $this->language->typer()->type($name, $example, $parent);
        switch ($goType) {
            case GOTyper::STRUCT:
                if ($this->language->configuration()->get(GOConfiguration::KEY_EmptyStructToInterface) &&
                    count(get_object_vars($example)) === 0) {
                    return new Types\InterfaceType($name, $example);
                } else {
                    return $this->parseStruct($name, $example, $parent);
                }
            case GOTyper::MAP:
                return $this->parseMap($name, $example, $parent);
            case GOTyper::SLICE:
                return $this->parseSlice($name, $example, $parent);
            case GOTyper::INTERFACE:
                return new Types\InterfaceType($name, $example, $parent);
            case GOTyper::RAWMESSAGE:
                return new Types\RawMessageType($name, $example);

            default:
                return new Types\SimpleType($name, $example, $goType);
        }
    }

    /**
     * @param string $name
     * @param \stdClass $example
     * @param \DCarbone\JSONModeler\TypeParent|null $parent
     * @return \DCarbone\JSONModeler\Languages\GO\Types\MapType
     */
    protected function parseMap(string $name, \stdClass $example, ?TypeParent $parent = null): Types\MapType {
        $mapType = new Types\MapType($name, $example, $parent);

        $varList = get_object_vars($example);
        $firstType = $this->language->typer()->type($name, reset($varList), $mapType);

        if (1 === count($varList)) {
            $type = $this->parse($name, reset($varList), $mapType);
        } else {
            $same = true;
            foreach ($varList as $k => $v) {
                $thisType = $this->language->typer()->type($name, $v, $mapType);
                if ($firstType !== $thisType) {
                    $same = false;
                    break;
                }
            }

            if ($same) {
                $type = $this->parse($name, reset($varList), $mapType);
            } else {
                $type = new Types\InterfaceType($name, reset($varList));
            }

        }

        $mapType->setMapType($type);

        return $mapType;
    }

    /**
     * @param string $name
     * @param \stdClass $example
     * @param \DCarbone\JSONModeler\TypeParent|null $parent
     * @return \DCarbone\JSONModeler\Languages\GO\Types\StructType
     */
    protected function parseStruct(string $name, \stdClass $example, ?TypeParent $parent = null): Types\StructType {
        $structType = new Types\StructType($name, $example, $parent);
        foreach (get_object_vars($example) as $childTypeName => $childTypeExample) {
            $structType->addChild($this->parse($childTypeName, $childTypeExample, $structType));
        }
        return $structType;
    }

    /**
     * @param string $name
     * @param array $example
     * @param \DCarbone\JSONModeler\TypeParent $parent
     * @return \DCarbone\JSONModeler\Languages\GO\Types\SliceType
     */
    protected function parseSlice(string $name, array $example, ?TypeParent $parent = null): Types\SliceType {
        $sliceType = new Types\SliceType($name, $example, $parent);
        $sliceType->setSliceType($this->parse($name, reset($example)));
        return $sliceType;
    }
}