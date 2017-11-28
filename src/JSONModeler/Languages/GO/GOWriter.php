<?php namespace DCarbone\JSONModeler\Languages\GO;

/*
 * Copyright (C) 2016-2017 Daniel Carbone (daniel.p.carbone@gmail.com)
 *
 * This software may be modified and distributed under the terms
 * of the MIT license.  See the LICENSE file for details.
 */

use DCarbone\JSONModeler\Language;
use DCarbone\JSONModeler\Type;
use DCarbone\JSONModeler\Writer;

/**
 * Class GOWriter
 * @package DCarbone\JSONModeler\Languages\GO
 */
class GOWriter implements Writer {
    /** @var \DCarbone\JSONModeler\Language */
    protected $language;

    /** @var bool */
    private $atRoot = true;

    /**
     * GOWriter constructor.
     * @param \DCarbone\JSONModeler\Language $language
     */
    public function __construct(Language $language) {
        $this->language = $language;
    }

    /**
     * @param \DCarbone\JSONModeler\Type $type
     * @param int $indentLevel
     * @return string
     */
    public function write(Type $type, int $indentLevel = 0): string {
        $output = '';

        $startedAtRoot = $this->atRoot;
        if ($startedAtRoot) {
            $this->atRoot = false;
            if ($this->language->configuration()->get(GOConfiguration::KEY_SingleTypeBlock)) {
                $output = "type (\n";
                $indentLevel++;
            }
        }

        if ($type instanceof Types\InterfaceType) {
            $output .= $this->writeInterface($type, $indentLevel);
        } else if ($type instanceof Types\MapType) {
            $output .= $this->writeMap($type, $indentLevel);
        } else if ($type instanceof Types\RawMessageType) {
            $output .= $this->writeRawMessage($type, $indentLevel);
        } else if ($type instanceof Types\SimpleType) {
            $output .= $this->writeSimple($type, $indentLevel);
        } else if ($type instanceof Types\SliceType) {
            $output .= $this->writeSlice($type, $indentLevel);
        } else if ($type instanceof Types\StructType) {
            $output .= $this->writeStruct($type, $indentLevel);
        } else {
            throw new \DomainException('Cannot write unknown type "'.get_class($type).'"');
        }

        if ($startedAtRoot && $this->language->configuration()->get(GOConfiguration::KEY_SingleTypeBlock)) {
            $output .= "\n)";
        }

        return $output;
    }

    /**
     * @param \DCarbone\JSONModeler\Languages\GO\Types\InterfaceType $type
     * @param int $indentLevel
     * @return string
     */
    protected function writeInterface(Types\InterfaceType $type, int $indentLevel = 0): string {
        /** @var \DCarbone\JSONModeler\Languages\GO\GONamer $namer */
        $namer = $this->language->namer();
        if (null === $type->parent()) {
            if ($this->language->configuration()->get(GOConfiguration::KEY_SingleTypeBlock)) {
                return sprintf("\t%s %s", $namer->typeName($type), $type->type());
            } else {
                return sprintf('type %s %s', $namer->typeName($type), $type->type());
            }
        }
        return $type->type();
    }

    /**
     * @param \DCarbone\JSONModeler\Languages\GO\Types\MapType $type
     * @param int $indentLevel
     * @return string
     */
    protected function writeMap(Types\MapType $type, int $indentLevel = 0): string {
        /** @var \DCarbone\JSONModeler\Languages\GO\GONamer $namer */
        $namer = $this->language->namer();

        $output = [];

        $mapType = $type->mapType();
        $parent = $type->parent();
        $singleTypeBlock = $this->language->configuration()->get(GOConfiguration::KEY_SingleTypeBlock);

        if ($this->language->configuration()->get(GOConfiguration::KEY_BreakOutInlineStructs)) {
            if ($mapType instanceof Types\StructType) {
                if ($singleTypeBlock) {
                    $output[] = sprintf("\t%s map[string]*%s", $namer->typeMapName($type), $namer->typeName($type));
                } else {
                    $output[] = sprintf('type %s map[string]*%s', $namer->typeMapName($type), $namer->typeName($type));
                }
                $output[] = $this->write($mapType, $indentLevel);
            } else if (null === $parent) {
                if ($singleTypeBlock) {
                    $output[] =
                        sprintf("\t%s map[string]%s", $namer->typeName($type), $this->write($mapType, $indentLevel));
                } else {
                    $output[] =
                        sprintf('type %s map[string]%s', $namer->typeName($type), $this->write($mapType, $indentLevel));
                }
            } else if ($parent instanceof Types\SliceType || $parent instanceof Types\MapType) {
                $output[] = sprintf('map[string]%s', $this->write($mapType, $indentLevel));
            } else {
                if ($singleTypeBlock) {
                    $output[] =
                        sprintf("\t%s map[string]%s", $namer->typeMapName($type), $this->write($mapType, $indentLevel));
                } else {
                    $output[] = sprintf('type %s map[string]%s',
                        $namer->typeMapName($type),
                        $this->write($mapType, $indentLevel));
                }
            }
        } else if (null === $parent) {
            if ($singleTypeBlock) {
                $output[] =
                    sprintf("\t%s map[string]%s", $namer->typeName($type), $this->write($mapType, $indentLevel));
            } else {
                $output[] =
                    sprintf('type %s map[string]%s', $namer->typeName($type), $this->write($mapType, $indentLevel));
            }
        } else {
            $output[] = sprintf('map[string]%s', $this->write($mapType, $indentLevel));
        }

        return implode("\n\n", $output);
    }

    /**
     * @param \DCarbone\JSONModeler\Languages\GO\Types\RawMessageType $type
     * @param int $indentLevel
     * @return string
     */
    protected function writeRawMessage(Types\RawMessageType $type, int $indentLevel = 0): string {
        /** @var \DCarbone\JSONModeler\Languages\GO\GONamer $namer */
        $namer = $this->language->namer();
        if (null === $type->parent()) {
            if ($this->language->configuration()->get(GOConfiguration::KEY_SingleTypeBlock)) {
                return sprintf("\t%s %s", $namer->typeName($type), $type->type());
            } else {
                return sprintf('type %s %s', $namer->typeName($type), $type->type());
            }
        }

        return $type->type();
    }

    /**
     * @param \DCarbone\JSONModeler\Languages\GO\Types\SimpleType $type
     * @param int $indentLevel
     * @return string
     */
    protected function writeSimple(Types\SimpleType $type, int $indentLevel = 0): string {
        /** @var \DCarbone\JSONModeler\Languages\GO\GONamer $namer */
        $namer = $this->language->namer();
        if (null === $type->parent()) {
            if ($this->language->configuration()->get(GOConfiguration::KEY_SingleTypeBlock)) {
                return sprintf(
                    "\t%s %s%s",
                    $namer->typeName($type),
                    $this->language->configuration()->get(GOConfiguration::KEY_ForceScalarToPointer) ? '*' : '',
                    $type->type()
                );
            } else {
                return sprintf(
                    'type %s %s%s',
                    $namer->typeName($type),
                    $this->language->configuration()->get(GOConfiguration::KEY_ForceScalarToPointer) ? '*' : '',
                    $type->type()
                );
            }
        }

        if ($this->language->configuration()->get(GOConfiguration::KEY_ForceScalarToPointer)) {
            return sprintf('*%s', $type->type());
        }

        return $type->type();
    }

    /**
     * @param \DCarbone\JSONModeler\Languages\GO\Types\SliceType $type
     * @param int $indentLevel
     * @return string
     */
    protected function writeSlice(Types\SliceType $type, int $indentLevel = 0): string {
        $output = [];

        $sliceType = $type->sliceType();
        $parent = $type->parent();
        $singleTypeBlock = $this->language->configuration()->get(GOConfiguration::KEY_SingleTypeBlock);

        /** @var \DCarbone\JSONModeler\Languages\GO\GONamer $namer */
        $namer = $this->language->namer();

        if ($this->language->configuration()->get(GOConfiguration::KEY_BreakOutInlineStructs)) {
            if ($sliceType instanceof Types\StructType) {
                if ($parent instanceof Types\SliceType) {
                    $output[] = sprintf('[]*%s', $namer->typeName($sliceType));
                } else {
                    if ($singleTypeBlock) {
                        $output[] = sprintf("\t%s []*%s", $namer->typeSliceName($type), $namer->typeName($type));
                    } else {
                        $output[] = sprintf('type %s []*%s', $namer->typeSliceName($type), $namer->typeName($type));
                    }
                }

                $output[] = $this->write($sliceType, $indentLevel);
            } else if (null === $parent) {
                if ($singleTypeBlock) {
                    $output[] = sprintf("\t%s []%s", $namer->typeName($type), $this->write($sliceType, $indentLevel));
                } else {
                    $output[] =
                        sprintf('type %s []%s', $namer->typeName($type), $this->write($sliceType, $indentLevel));
                }
            } else if ($parent instanceof Types\SliceType || $parent instanceof Types\MapType) {
                $output[] = sprintf('[]%s', $this->write($sliceType, $indentLevel));
            } else {
                if ($singleTypeBlock) {
                    $output[] =
                        sprintf("\t%s []%s", $namer->typeSliceName($type), $this->write($sliceType, $indentLevel));
                } else {
                    $output[] =
                        sprintf('type %s []%s', $namer->typeSliceName($type), $this->write($sliceType, $indentLevel));
                }
            }
        } else if (null === $parent) {
            if ($singleTypeBlock) {
                $output[] = sprintf("\t%s []%s", $namer->typeName($type), $this->write($sliceType, $indentLevel));
            } else {
                $output[] = sprintf('type %s []%s', $namer->typeName($type), $this->write($sliceType, $indentLevel));
            }
        } else {
            $output[] = sprintf('[]%s', $this->write($sliceType, $indentLevel));
        }

        return implode("\n\n", $output);
    }

    /**
     * @param \DCarbone\JSONModeler\Languages\GO\Types\StructType $type
     * @param int $indentLevel
     * @return string
     */
    protected function writeStruct(Types\StructType $type, int $indentLevel = 0): string {
        /** @var \DCarbone\JSONModeler\Languages\GO\GONamer $namer */
        $namer = $this->language->namer();
        /** @var \DCarbone\JSONModeler\Languages\GO\GOConfiguration $configuration */
        $configuration = $this->language->configuration();

        $output = [];

        $breakOutInlineStructs = $configuration->get(GOConfiguration::KEY_BreakOutInlineStructs);
        $singleTypeBlock = $configuration->get(GOConfiguration::KEY_SingleTypeBlock);
        $parent = $type->parent();

        if ($breakOutInlineStructs || null === $parent) {
            if ($singleTypeBlock) {
                $go = sprintf("\t%s %s{\n", $namer->typeName($type), $type->type());
            } else {
                $go = sprintf("type %s %s {\n", $namer->typeName($type), $type->type());
            }
        } else {
            $go = sprintf("%s {\n", $type->type());
        }

        foreach ($type->fields() as $field) {
            if ($configuration->isFieldIgnored($type, $field)) {
                $configuration->logger()->info(sprintf('[json-to-go] Ignoring field "%s" in struct "%s"',
                    $field->name(),
                    $type->name()));
                continue;
            }

            $configuration->logger()->debug(sprintf('[json-to-go] Writing field "%s" in struct "%s"',
                $field->name(),
                $type->name()));

            $exported = $configuration->isFieldExported($type, $field);

            $go = sprintf(
                '%s%s%s',
                $go,
                $this->indents($indentLevel + 1),
                $exported ? $namer->goName($field) : lcfirst($namer->goName($field))
            );

            $fieldTag = $configuration->buildFieldTag($type, $field);

            $fieldTag = trim($fieldTag, " \t\n\r\0\x0B`");
            if ('' !== $fieldTag) {
                $fieldTag = sprintf(' `%s`', $fieldTag);
            }

            if ($breakOutInlineStructs &&
                !($field instanceof Types\SimpleType || $field instanceof Types\InterfaceType)) {
                // Add the child struct to the output list...
                $output[] = $this->write($field, $indentLevel);

                if ($field instanceof Types\StructType) {
                    $go = sprintf('%s *%s%s', $go, $namer->typeName($field), $fieldTag);
                } else if ($field instanceof Types\SliceType) {
                    $go = sprintf('%s %s%s', $go, $namer->typeSliceName($field), $fieldTag);
                } else if ($field instanceof Types\MapType) {
                    $go = sprintf('%s %s%s', $go, $namer->typeMapName($field), $fieldTag);
                }
            } else {
                $go = sprintf('%s %s%s', $go, $this->write($field, $indentLevel + 2), $fieldTag);
            }

            $go = sprintf("%s\n", $go);
        }

        $output[] = sprintf("%s\n%s}", rtrim($go), $this->indents($indentLevel));

        return implode("\n\n", $output);
    }

    /**
     * @param int $level
     * @return string
     */
    protected function indents(int $level): string {
        if (0 >= $level) {
            return '';
        } else {
            return str_repeat("\t", $level);
        }
    }
}