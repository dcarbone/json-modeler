<?php namespace DCarbone;

/*
 * Copyright (C) 2016-2017 Daniel Carbone (daniel.p.carbone@gmail.com)
 *
 * This software may be modified and distributed under the terms
 * of the MIT license.  See the LICENSE file for details.
 */

use DCarbone\JSONModeler\Language;
use DCarbone\JSONModeler\Type;

/**
 * Class JSONModeler
 * @package DCarbone
 */
class JSONModeler {

    const TYPE_NAME_REGEX = '^[a-zA-Z][a-zA-Z0-9]*$';

    /** @var \DCarbone\JSONModeler\Language[] */
    protected $languages = [];

    /**
     * JSONModeler constructor.
     * @param array $languages
     */
    public function __construct(array $languages = []) {
        if (count($languages) === 0) {
            foreach (glob(__DIR__.'/JSONModeler/Languages/*', GLOB_NOSORT | GLOB_ONLYDIR) as $langDir) {
                $lang = trim(strrchr($langDir, '/'), "/");
                if (file_exists(($langFile = "{$langDir}/{$lang}Language.php"))) {
                    $confClass = __NAMESPACE__.'\\'.__CLASS__."\\Languages\\{$lang}Configuration";
                    $langClass = __NAMESPACE__.'\\'.__CLASS__."\\Languages\\{$lang}Language";
                    $this->addLanguage(new $langClass(new $confClass));
                } else {
                    throw new \RuntimeException("You didn't property name the language file for \"{$lang}\", fix it.");
                }
            }
        } else {
            foreach ($languages as $language) {
                $this->addLanguage($language);
            }
        }
    }

    /**
     * @param string $name
     * @return \DCarbone\JSONModeler\Language|null
     */
    public function language(string $name): ?Language {
        return $this->languages[$name] ?? null;
    }

    /**
     * @param \DCarbone\JSONModeler\Language $language
     */
    public function addLanguage(Language $language) {
        $this->languages[$language->name()] = $language;
    }

    /**
     * Convenience method that attempts to json_encode the input before generation
     *
     * @param string $typeName
     * @param mixed $decodedInput
     * @param string $language
     * @return \DCarbone\JSONModeler\Type
     */
    public function parseDecoded(string $typeName, $decodedInput, string $language): Type {
        $encoded = @json_encode($decodedInput);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \RuntimeException('Unable to json_encode input: '.json_last_error_msg());
        }
        return $this->parse($typeName, $encoded, $language);
    }

    /**
     * @param string $typeName
     * @param string $input
     * @param string $language
     * @return \DCarbone\JSONModeler\Type
     */
    public function parse(string $typeName, string $input, string $language): Type {
        $lang = $this->language($language);
        if (!$lang) {
            throw new \RuntimeException("No language named \"{$language}\" defined.");
        }

        $typeName = trim($typeName);
        if ('' === $typeName || !preg_match('/'.self::TYPE_NAME_REGEX.'/', $typeName)) {
            throw new \InvalidArgumentException(get_class($this).
                '::generate - Root type name must follow "'.self::TYPE_NAME_REGEX.'", '.
                $typeName.
                ' does not.');
        }

        $input = trim($input);
        if ('' === $input) {
            throw new \RuntimeException(get_class($this).
                '::generate - Input is empty, please re-construct with valid input');
        }

        $decoded = json_decode($input);
        if (JSON_ERROR_NONE !== json_last_error()) {
            throw new \RuntimeException(get_class($this).
                '::generate - Unable to json_decode input: '.
                json_last_error_msg());
        }

        return $lang->parser()->parse($typeName, $this->sanitizeInput($decoded));
    }

    /**
     * @param string $typeName
     * @param string $input
     * @param string $language
     * @return string
     */
    public function generate(string $typeName, string $input, string $language): string {
        $lang = $this->language($language);
        if (!$lang) {
            throw new \RuntimeException("No language named \"{$language}\" defined.");
        }
        return $lang->writer()->write($this->parse($typeName, $input, $language));
    }

    /**
     * @param $typeExample
     * @return array|bool|float|int|null|string
     */
    protected function sanitizeInput($typeExample) {
        switch (gettype($typeExample)) {
            case 'string':
                return 'string';
            case 'double':
                return 1.1;
            case 'integer':
                return 1;
            case 'boolean':
                return true;

            case 'array':
                return $this->sanitizeArrayItems($typeExample);

            case 'object':
                $tmp = $typeExample;
                foreach (get_object_vars($tmp) as $k => $v) {
                    $tmp->{$k} = $this->sanitizeInput($v);
                }
                $forTheSorts = json_decode(json_encode($tmp), true);
                ksort($forTheSorts, SORT_NATURAL);
                return json_decode(json_encode($forTheSorts));

            default:
                return null;
        }
    }

    /**
     * @param array $example
     * @return array
     */
    protected function sanitizeArrayItems(array $example): array {
        $arrayType = null;
        foreach ($example as $item) {
            if ($item === null) {
                continue;
            }
            $itemType = gettype($item);
            if (!isset($arrayType)) {
                $arrayType = $itemType;
            } else if ($arrayType === 'integer' && $itemType === 'double') {
                $arrayType = 'double';
            } else if ($arrayType === 'double' && $itemType === 'integer') {
                continue;
            } else if ($arrayType !== $itemType) {
                return [null];
            }
        }

        switch ($arrayType) {
            case 'object':
                return [$this->combineArrayObjects($example)];
            case 'array':
                $subExample = [];
                foreach ($example as $subExample) {
                    foreach ($subExample as $subItem) {
                        $subExample[] = $subItem;
                    }
                }
                return [$this->sanitizeArrayItems($subExample)];
            case 'double':
                return [1.1];
            case 'integer':
                return [1];

            default:
                return [$this->sanitizeInput(reset($example))];
        }
    }

    /**
     * @param array $example
     * @return \stdClass
     */
    protected function combineArrayObjects(array $example): \stdClass {
        $type = new \stdClass();
        foreach ($example as $object) {
            foreach (get_object_vars($object) as $field => $fieldExample) {
                if (!property_exists($type, $field)) {
                    $type->{$field} = $fieldExample;
                } else if ($fieldExample === null) {
                    continue;
                } else if ($type->{$field} === null && $fieldExample !== null) {
                    $type->{$field} = $fieldExample;
                } else if (gettype($type->{$field}) === 'integer' && gettype($fieldExample) === 'double') {
                    $type->{$field} = 1.0;
                } else if (gettype($type->{$field}) === 'double' && gettype($fieldExample) === 'integer') {
                    continue;
                } else if (gettype($type->{$field}) !== gettype($fieldExample)) {
                    $type->{$field} = null;
                    break;
                }
            }
        }

        foreach (get_object_vars($type) as $field => $fieldExample) {
            $type->{$field} = $this->sanitizeInput($fieldExample);
        }

        $forTheSorts = json_decode(json_encode($type), true);
        ksort($forTheSorts, SORT_NATURAL);
        return json_decode(json_encode($forTheSorts));
    }
}