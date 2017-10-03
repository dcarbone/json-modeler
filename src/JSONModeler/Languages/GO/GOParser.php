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
use DCarbone\JSONModeler\Languages\GO\Types\RawMessageType;
use DCarbone\JSONModeler\Languages\GO\Types\SimpleType;
use DCarbone\JSONModeler\Languages\GO\Types\SliceType;
use DCarbone\JSONModeler\Languages\GO\Types\StructType;
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
                    return new InterfaceType($name, $example);
                } else {
                    return $this->parseStruct($name, $example, $parent);
                }
            case GOTyper::MAP:
                return $this->parseMap($name, $example, $parent);
            case GOTyper::SLICE:
                return $this->parseSlice($name, $example, $parent);
            case GOTyper::INTERFACE:
                return new InterfaceType($name, $example, $parent);
            case GOTyper::RAWMESSAGE:
                return new RawMessageType($name, $example);

            default:
                return new SimpleType($name, $example, $goType);
        }
    }

    /**
     * @param string $name
     * @param \stdClass $example
     * @param \DCarbone\JSONModeler\TypeParent|null $parent
     * @return \DCarbone\JSONModeler\Languages\GO\Types\MapType
     */
    protected function parseMap(string $name, \stdClass $example, ?TypeParent $parent = null): MapType {
        $mapType = new MapType($name, $example, $parent);

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
                $type = new InterfaceType($name, reset($varList));
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
    protected function parseStruct(string $name, \stdClass $example, ?TypeParent $parent = null): StructType {
        $structType = new StructType($name, $example, $parent);
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
    protected function parseSlice(string $name, array $example, ?TypeParent $parent = null): SliceType {
        $sliceType = new SliceType($name, $example, $parent);
        $sliceType->setSliceType($this->parse($name, reset($example)));
        return $sliceType;
    }
}