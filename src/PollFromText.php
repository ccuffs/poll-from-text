<?php

namespace CCUFFS\Text;

/**
 * 
 *
 * @author Fernando Bevilacqua <fernando.bevilacqua@uffs.edu.br>
 */
class PollFromText
{
    public static function make($text, array $config = []) {
        return (new PollFromText())->parse($text, $config);
    }

    protected function split($text)
    {
        return preg_split('/\R+/', $text, 0, PREG_SPLIT_NO_EMPTY);
    }

    protected function findJsonStringStartingAt($startIndex, $text) {
        $json = '';
        $chars = 0;
        $braces = 0;

        if ($text[$startIndex] != '{') {
            return '';
        }

        do {
            $currentChar = $text[$startIndex];
            $json .= $currentChar;

            if ($currentChar == '{') { $braces++; }
            if ($currentChar == '}') { $braces--; }

            if ($braces == 0) {
                return $json;
            }

        } while ($startIndex++ < strlen($text));

        return $text;
    }

    protected function fillStructureWithDataAttribute(& $structure) {
        $text = trim($structure['text']);
        $textFirstChar = $text[0];

        if ($textFirstChar != '{') {
            return false;
        }

        // If we reached this point, we have something like the following:
        // {...} text here
        // {...} text here { this should be text } again

        $data = $this->findJsonStringStartingAt(0, $text);

        if (empty($data)) {
            return false;
        }

        $textWithoutAttr = str_replace($data, '', $text);

        $structure['text'] = trim($textWithoutAttr);
        $structure['data'] = $this->decodeAttribute($textWithoutAttr, $data);

        return true;
    }

    protected function fillStructureWithOption(& $structure) {
        $text = trim($structure['text']);
        $firstChar = $text[0];
        $optionChars = ['-', '*']; // TODO: make a config?

        if (in_array($firstChar, $optionChars)) {
            // This is a option like the following:
            // - my option
            // * an option
            $structure['text'] = trim(substr($text, 1));
            $structure['type'] = 'option';

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

        $structure['text'] = $text;
        $structure['value'] = $value;
        $structure['type'] = 'option';
        return true;
    }

    protected function extractStructure($text) {
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
        $this->fillStructureWithDataAttribute($structure);

        // Something like the following:
        //   a) option text
        //   1) option text
        //   no_space_here) option text
        //   - option text
        //   * option text
        $this->fillStructureWithOption($structure);

        return $structure;
    }

    protected function decodeAttribute($text, $dataAttr) {
        try {
            $flags = JSON_THROW_ON_ERROR | JSON_NUMERIC_CHECK;
            $data = json_decode($dataAttr, true, 512, $flags);
            return $data;
        } catch (\JsonException $e) {
            throw new \UnexpectedValueException("Data attribute '$dataAttr' is not valid JSON in use at text '$text'.", 1, $e);
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

        if (!empty($structure['data'])) {
            $text = ['text' => $text, 'data' => $structure['data']];
        }        

        if (!empty($structure['value'])) {
            $key = $structure['value'];
            return [
                $key => $text
            ];
        }
        
        return [$text];
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
     * 
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

            $structure = $this->extractStructure($currentLine);
            $currentType = $structure['type'];

            if ($currentType == 'question') {
                if($previousType == 'question' && @$config['mutliline_question']) {
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
