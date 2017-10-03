<?php namespace DCarbone\JSONModeler\Languages\GO;

/*
 * Copyright (C) 2016-2017 Daniel Carbone (daniel.p.carbone@gmail.com)
 *
 * This software may be modified and distributed under the terms
 * of the MIT license.  See the LICENSE file for details.
 */

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
    /** @var \DCarbone\JSONModeler\Languages\GO\GOConfiguration */
    protected $configuration;
    /** @var \DCarbone\JSONModeler\Languages\GO\GOTyper */
    protected $typer;

    /**
     * GOParser constructor.
     * @param \DCarbone\JSONModeler\Languages\GO\GOConfiguration $configuration
     * @param \DCarbone\JSONModeler\Languages\GO\GOTyper $typer
     */
    public function __construct(GOConfiguration $configuration, GOTyper $typer) {
        $this->configuration = $configuration;
        $this->typer = $typer;
    }

    /**
     * @param string $name
     * @param $example
     * @param \DCarbone\JSONModeler\TypeParent|null $parent
     * @return \DCarbone\JSONModeler\Type
     */
    public function parse(string $name, $example, ?TypeParent $parent = null): Type {
        $goType = $this->typer->type($name, $example, $parent);
        switch ($goType) {
            case GOTyper::STRUCT:
                if ($this->configuration->get(GOConfiguration::KEY_EmptyStructToInterface) &&
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
        $firstType = $this->typer->type($name, reset($varList), $mapType);

        if (1 === count($varList)) {
            $type = $this->parse($name, reset($varList), $mapType);
        } else {
            $same = true;
            foreach ($varList as $k => $v) {
                $thisType = $this->typer->type($name, $v, $mapType);
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

        $sliceGoType = null;
        $sliceLength = count($example);

        foreach ($example as $item) {
            $thisType = $this->parse($name, $item, $sliceType);

            if (null === $sliceGoType) {
                $sliceGoType = $thisType;
            } else {
                $sliceGoType = $this->typer->mostSpecificPossibleGoType($thisType, $sliceGoType);

                if ($sliceGoType instanceof InterfaceType) {
                    break;
                }
            }
        }

        if ($sliceGoType instanceof StructType || $sliceGoType instanceof MapType) {
            $allFields = [];

            foreach ($example as $item) {
                foreach (get_object_vars($item) as $key => $value) {
                    if (!isset($allFields[$key])) {
                        $allFields[$key] = [
                            'value' => $value,
                            'count' => 0,
                        ];
                    }

                    $allFields[$key]['count']++;
                }
            }

            if ($sliceGoType instanceof StructType &&
                $this->configuration->get(GOConfiguration::KEY_EmptyStructToInterface) &&
                0 === count($allFields)) {
                $type = new InterfaceType($name, $example);
            } else {
                $childTypeExample = new \stdClass();

                $omitempty = [];
                foreach (array_keys($allFields) as $key) {
                    $childTypeExample->$key = $allFields[$key]['value'];
                    $omitempty[$key] = $allFields[$key]['count'] !== $sliceLength;
                }

                if ($sliceGoType instanceof StructType) {
                    $type = $this->parseStruct($name, $childTypeExample, $sliceType);
                    foreach ($type->fields() as $field) {
                        if ($omitempty[$field->name()]) {
                            $field->notAlwaysDefined();
                        }
                    }
                } else {
                    $type = $this->parseMap($name, $childTypeExample, $sliceType);
                }
            }
        } else if ($sliceGoType instanceof SliceType) {
            if (2 > count($example)) {
                // if there is no example or only one example, no further parsing is needed
                $type = $this->parse($name, reset($typeExample), $sliceType);
            } else {
                // if we have more than one child of this slice, loop through and ensure that all child
                $sliceSubTypeList = [];

                foreach ($example as $i => $subType) {
                    $sliceSubTypeList[] = $this->parse($name, $subType, $sliceType);
                }

                $type = null;

                foreach ($sliceSubTypeList as $sliceSubType) {
                    if (null === $type) {
                        $type = $sliceSubType;
                    } else if (get_class($type) === get_class($sliceSubType)) {
                        $type = $this->typer->mostSpecificPossibleGoType($type, $sliceSubType);
                        if ($type instanceof InterfaceType) {
                            break;
                        }
                    }
                }
            }
        } else if ($sliceGoType instanceof InterfaceType) {
            $type = new InterfaceType($name, $example);
        } else {
            if ($sliceGoType) {
                $type = new SimpleType($name, $example, $sliceGoType->type());
            } else {
                $type = new InterfaceType($name, $example);
            }
        }

        $sliceType->setSliceType($type);

        return $sliceType;
    }
}