<?php

namespace CCUFFS\Text;

/**
 * Class to parse semi-structured text into structured-data that can be used to build questionnaires (forms).
 * The main goal is to allow end users to build dynamic forms, e.g. Google Forms, using plain text like they
 * would if the forms were to be printed on paper.
 *
 * @author Fernando Bevilacqua <fernando.bevilacqua@uffs.edu.br>
 * @license MIT
 */
class PollFromText
{
    /**
     * Allow question/answer attributes to be any text.
     */
    public const ATTR_AS_TEXT = 1;
    
    /**
     * Force question/answer attributes to be a valid JSON string.
     */    
    public const ATTR_AS_STRICT_JSON = 2;

    /**
     * Lista of available attribute configurations.
     */
    private array $attrConfigs = [
        self::ATTR_AS_TEXT,
        self::ATTR_AS_STRICT_JSON
    ];

    /**
     * @param mixed $text text to be parsed into a questionnaire
     * @param array $config specific configuration for this parsing, e.g. allow multiline questions.
     * 
     * @return array associative array containing the structured questionnaire.
     */
    public static function make($text, array $config = []) {
        return (new PollFromText())->parse($text, $config);
    }

    protected function split($text)
    {
        return preg_split('/\R+/', $text, 0, PREG_SPLIT_NO_EMPTY);
    }

    protected function findDataStringStartingAt($startIndex, $text) {
        $data = '';
        $chars = 0;
        $braces = 0;

        if ($text[$startIndex] != '{') {
            return '';
        }

        do {
            $currentChar = $text[$startIndex];
            $data .= $currentChar;

            if ($currentChar == '{') { $braces++; }
            if ($currentChar == '}') { $braces--; }

            if ($braces == 0) {
                return $data;
            }

        } while ($startIndex++ < strlen($text));

        return $text;
    }

    protected function fillStructureWithDataAttribute(& $structure, array $config = []) {
        $text = trim($structure['text']);
        $textFirstChar = $text[0];

        if ($textFirstChar != '{') {
            return false;
        }

        // If we reached this point, we have something like the following:
        // {...} text here
        // {...} text here { this should be text } again

        $data = $this->findDataStringStartingAt(0, $text);

        if (empty($data)) {
            return false;
        }

        $textWithoutAttr = str_replace($data, '', $text);

        $structure['text'] = trim($textWithoutAttr);
        $structure['data'] = $this->decodeAttribute($textWithoutAttr, $data, $config);

        return true;
    }

    protected function fillStructureWithOption(& $structure, array $config = []) {
        $text = trim($structure['text']);
        $firstChar = $text[0];
        $optionChars = ['-', '*']; // TODO: make a config?

        if (in_array($firstChar, $optionChars)) {
            // This is a option like the following:
            // - my option
            // * an option
            $structure['text'] = trim(substr($text, 1));
            $structure['type'] = 'option';
            $structure['marker'] = $firstChar;

            return true;
        }

        // Here we still might have options like:
        //   a) option text
        //   1) option text
        //   no_space_here) option text
        $separator = ')'; // TODO: make a config? Multiple separators (array)?
        $separatorIndex = stripos($text, $separator);

        if ($separatorIndex === false) {
            return false;
        }

        // This is a option with a separator, e.g. a)
        $value = substr($text, 0, $separatorIndex);
        $text = trim(substr($text, $separatorIndex + 1));

        $valueHasSpace = stripos($value, ' ') !== false;

        if ($valueHasSpace) {
            return false;
        }

        $value = trim($value);

        $structure['text'] = $text;
        $structure['value'] = $value;
        $structure['type'] = 'option';
        $structure['marker'] = $value;
        $structure['separator'] = $separator;

        return true;

    }

    protected function extractStructure($text, array $config = []) {
        $text = trim($text);
        $structure = [
            'type' => 'question',
            'text' => $text
        ];

        if (empty($text)) {
            return $structure;
        }

        // Something like the following:
        // {...} text here
        // {...} text here { this should be text } again
        $this->fillStructureWithDataAttribute($structure, $config);

        // Something like the following:
        //   a) option text
        //   1) option text
        //   no_space_here) option text
        //   - option text
        //   * option text
        $this->fillStructureWithOption($structure, $config);

        return $structure;
    }

    protected function removeDataGuardChars($text) {
        $text = trim($text);
        return trim(substr($text, 1, -1));
    }

    protected function decodeAttribute($text, $dataAttr, array $config = []) {
        $requestedAttrValidation = isset($config['attr_validation']) ? $config['attr_validation'] : self::ATTR_AS_TEXT;

        foreach($this->attrConfigs as $availableAttrConfig) {
            $requestedThisAttrValidation = ($requestedAttrValidation & $availableAttrConfig) != 0;

            if (!$requestedThisAttrValidation) {
                continue;
            }

            if ($availableAttrConfig == self::ATTR_AS_TEXT) {
                return $this->removeDataGuardChars($dataAttr);

            } else if ($availableAttrConfig == self::ATTR_AS_STRICT_JSON) {
                try {
                    $flags = JSON_THROW_ON_ERROR | JSON_NUMERIC_CHECK;
                    $data = json_decode($dataAttr, true, 512, $flags);
                    return $data;

                } catch (\JsonException $e) {
                    throw new \UnexpectedValueException("Data attribute '$dataAttr' is not valid JSON in use at text '$text'.", 1, $e);
                }
            }
        }
    }

    protected function createQuestion(array $structure) {
        $item = [
            'text' => $structure['text'],
            'type' => 'input'
        ];

        if (isset($structure['data'])) {
            $item['data'] = $structure['data'];
        }

        return $item;
    }

    protected function amendPreviousQuestion(& $questions, array $structure) {
        $text = $structure['text'];
        $index = count($questions) - 1;
        $questions[$index]['text'] .= ' ' . $text;
    }    

    protected function createOption(array $structure) {
        $text = trim($structure['text']);

        $entry = [
            'text' => $text,
        ];

        if (isset($structure['marker'])) {
            $entry['marker'] = $structure['marker'];
        }

        if (isset($structure['separator'])) {
            $entry['separator'] = $structure['separator'];
        }

        if (!empty($structure['data'])) {
            $entry['data'] = $structure['data'];
        }

        if (!empty($structure['value'])) {
            $key = $structure['value'];
            return [
                $key => $entry
            ];
        }
        
        return [$entry];
    }

    protected function addOptionToPreviousQuestion(& $questions, array $structure) {
        if (count($questions) == 0) {
            throw new \Exception('Unexepect option, e.g. "- abc...", "a) abc", without preceding simple text, e.g. "abc..."');
        }

        $index = count($questions) - 1;
        $questions[$index]['type'] = 'select';

        if (!isset($questions[$index]['options'])) {
            $questions[$index]['options'] = [];
        }

        $questions[$index]['options'] = array_merge($questions[$index]['options'], $this->createOption($structure));
    }

    /**
     * Parse the given text and return an array of questions.
     * 
     * @param mixed $text text to be parsed into a questionnaire
     * @param array $config specific configuration for this parsing, e.g. allow multiline questions.
     * 
     * @return array associative array containing the structured questionnaire.
     */
    public function parse($text, array $config = [])
    {
        $text = trim($text);

        if(empty($text)) {
            return [];
        }

        $questions = [];
        $parts = $this->split($text);
        $previousType = '';
        $currentType = '';

        for($i = 0, $size = count($parts); $i < $size; $i++) {
            $currentLine = trim($parts[$i]);

            if (empty($currentLine)) {
                $currentType = '';
                $previousType = 'empty';                
                continue;
            }

            $structure = $this->extractStructure($currentLine, $config);
            $currentType = $structure['type'];

            if ($currentType == 'question') {
                if($previousType == 'question' && @$config['multiline_question']) {
                    $this->amendPreviousQuestion($questions, $structure);
                } else {
                    $questions[] = $this->createQuestion($structure);
                }
            } else if ($currentType == 'option') {
                $this->addOptionToPreviousQuestion($questions, $structure);
            }

            $previousType = $currentType;
        }

        return $questions;
    }
}
