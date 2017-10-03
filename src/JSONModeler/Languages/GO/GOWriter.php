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
use DCarbone\JSONModeler\Type;
use DCarbone\JSONModeler\Writer;

/**
 * Class GOWriter
 * @package DCarbone\JSONModeler\Languages\GO
 */
class GOWriter implements Writer {
    /** @var \DCarbone\JSONModeler\Language */
    protected $language;

    /**
     * GOWriter constructor.
     * @param \DCarbone\JSONModeler\Language $language
     */
    public function __construct(Language $language) {
        $this->language = $language;
    }

    public function write(Type $type, int $indentLevel = 0): string {
        if ($type instanceof InterfaceType) {
            return $this->writeInterface($type, $indentLevel);
        } else if ($type instanceof MapType) {
            return $this->writeMap($type, $indentLevel);
        } else if ($type instanceof RawMessageType) {
            return $this->writeRawMessage($type, $indentLevel);
        } else if ($type instanceof SimpleType) {
            return $this->writeSimple($type, $indentLevel);
        } else if ($type instanceof SliceType) {
            return $this->writeSlice($type, $indentLevel);
        } else if ($type instanceof Types\StructType) {
            return $this->writeStruct($type, $indentLevel);
        } else {
            throw new \DomainException('Cannot write unknown type "' . get_class($type) . '"');
        }
    }

    /**
     * @param \DCarbone\JSONModeler\Languages\GO\Types\InterfaceType $type
     * @param int $indentLevel
     * @return string
     */
    protected function writeInterface(Types\InterfaceType $type, int $indentLevel = 0): string {
        if (null === $type->parent()) {
            return sprintf('type %s %s', $this->language->namer()->typeName($type), $type->type());
        }

        return $type->type();
    }

    /**
     * @param \DCarbone\JSONModeler\Languages\GO\Types\MapType $type
     * @param int $indentLevel
     * @return string
     */
    protected function writeMap(Types\MapType $type, int $indentLevel = 0): string {
        $output = [];

        $mapType = $type->mapType();

        $parent = $type->parent();

        if ($this->language->configuration()->get(GOConfiguration::KEY_BreakOutInlineStructs)) {
            if ($mapType instanceof StructType) {
                $output[] =
                    sprintf('type %s map[string]*%s', $this->language->namer()->typeMapName($type), $this->namer->typeName($type));
                $output[] = $this->write($mapType, $indentLevel);
            } else if (null === $parent) {
                $output[] = sprintf('type %s map[string]%s', $this->language->namer()->typeName($type), $this->write($mapType));
            } else if ($parent instanceof SliceType || $parent instanceof MapType) {
                $output[] = sprintf('map[string]%s', $this->write($mapType, $indentLevel));
            } else {
                $output[] = sprintf('type %s map[string]%s', $this->language->namer()->typeMapName($type), $this->write($mapType));
            }
        } else if (null === $parent) {
            $output[] = sprintf(
                'type %s map[string]%s',
                $this->language->namer()->typeName($type),
                $this->write($mapType, $indentLevel)
            );
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
    protected function writeRawMessage(RawMessageType $type, int $indentLevel = 0): string {
        if (null === $type->parent()) {
            return sprintf(
                'type %s %s',
                $this->language->namer()->typeName($type),
                $type->type()
            );
        }

        return $type->type();
    }

    /**
     * @param \DCarbone\JSONModeler\Languages\GO\Types\SimpleType $type
     * @param int $indentLevel
     * @return string
     */
    protected function writeSimple(SimpleType $type, int $indentLevel = 0): string {
        if (null === $type->parent()) {
            return sprintf(
                'type %s %s%s',
                $this->language->namer()->typeName($type),
                $this->language->configuration()->get(GOConfiguration::KEY_ForceScalarToPointer) ? '*' : '',
                $type->type()
            );
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
    protected function writeSlice(SliceType $type, int $indentLevel = 0): string {
        $output = [];

        $sliceType = $type->sliceType();
        $parent = $type->parent();

        if ($this->language->configuration()->get(GOConfiguration::KEY_BreakOutInlineStructs)) {
            if ($sliceType instanceof StructType) {
                if ($parent instanceof SliceType) {
                    $output[] = sprintf('[]*%s', $this->language->namer()->typeName($sliceType));
                } else {
                    $output[] =
                        sprintf('type %s []*%s', $this->language->namer()->typeSliceName($type), $this->namer->typeName($type));
                }

                $output[] = $this->write($sliceType, $indentLevel);
            } else if (null === $parent) {
                $output[] = sprintf('type %s []%s', $this->language->namer()->typeName($type), $this->write($sliceType));
            } else if ($parent instanceof SliceType || $parent instanceof MapType) {
                $output[] = sprintf('[]%s', $this->write($sliceType, $indentLevel));
            } else {
                $output[] = sprintf('type %s []%s', $this->language->namer()->typeSliceName($type), $this->write($sliceType));
            }
        } else if (null === $parent) {
            $output[] = sprintf(
                'type %s []%s',
                $this->language->namer()->typeName($type),
                $this->write($sliceType, $indentLevel)
            );
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
    protected function writeStruct(StructType $type, int $indentLevel = 0): string {
        $output = [];

        $breakOutInlineStructs = $this->language->configuration()->get(GOConfiguration::KEY_BreakOutInlineStructs);
        $parent = $type->parent();

        if ($breakOutInlineStructs || null === $parent) {
            $go = sprintf(
                "type %s %s {\n",
                $this->language->namer()->typeName($type),
                $type->type()
            );
        } else {
            $go = sprintf(
                "%s {\n",
                $type->type()
            );
        }

        foreach ($type->fields() as $field) {
            if ($this->language->configuration()->isFieldIgnored($type, $field)) {
                $this->language->configuration()->logger()->info(sprintf('[json-to-go] Ignoring field "%s" in struct "%s"',
                    $field->name(),
                    $type->name()));
                continue;
            }

            $this->language->configuration()->logger()->debug(sprintf('[json-to-go] Writing field "%s" in struct "%s"',
                $field->name(),
                $type->name()));

            $exported = $this->language->configuration()->isFieldExported($type, $field);

            $go = sprintf(
                '%s%s%s',
                $go,
                $this->indents($indentLevel + 1),
                $exported ? $this->language->namer()->goName($field) : lcfirst($this->namer->goName($field))
            );

            $fieldTag = $this->language->configuration()->buildFieldTag($type, $field);

            $fieldTag = trim($fieldTag, " \t\n\r\0\x0B`");
            if ('' !== $fieldTag) {
                $fieldTag = sprintf(' `%s`', $fieldTag);
            }

            if ($breakOutInlineStructs && !($field instanceof SimpleType || $field instanceof InterfaceType)) {
                // Add the child struct to the output list...
                $output[] = $this->write($field);

                if ($field instanceof StructType) {
                    $go = sprintf(
                        '%s *%s%s',
                        $go,
                        $this->language->namer()->typeName($field),
                        $fieldTag
                    );
                } else if ($field instanceof SliceType) {
                    $go = sprintf(
                        '%s %s%s',
                        $go,
                        $this->language->namer()->typeSliceName($field),
                        $fieldTag
                    );
                } else if ($field instanceof MapType) {
                    $go = sprintf(
                        '%s %s%s',
                        $go,
                        $this->language->namer()->typeMapName($field),
                        $fieldTag
                    );
                }
            } else {
                $go = sprintf(
                    '%s %s%s',
                    $go,
                    $this->write($field, $indentLevel + 2),
                    $fieldTag
                );
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