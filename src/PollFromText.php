<?php

namespace CCUFFS\Text;

/**
 * 
 *
 * @author Fernando Bevilacqua <fernando.bevilacqua@uffs.edu.br>
 */
class PollFromText
{
    protected function split($text)
    {
        return preg_split('/\R+/', $text, 0, PREG_SPLIT_NO_EMPTY);
    }

    protected function extractStructure($text) {
        $re = '/^(\*|-|\{(.*)\}|(.*)\))/m';

        $text = trim($text);
        preg_match_all($re, $text, $matches, PREG_SET_ORDER, 0);

        if (count($matches) == 0) {
            return [
                'type' => 'question',
                'text' => $text
            ];
        }

        $matchedItem = $matches[0][0];

        if ($matchedItem == '*' || $matchedItem == '-') {
            $indexFirstSpace = stripos($text, ' ');
            return [
                'type' => 'option',
                'text' => substr($text, $indexFirstSpace)
            ];
        }

        foreach([')', '}'] as $separator) {
            if (stripos($matchedItem, $separator) === false) {
                continue;
            }

            $indexFirstSeparator = stripos($text, $separator);
            $text = trim(substr($text, $indexFirstSeparator + 1));
            $data = $matches[0][1];

            return [
                'type' => $separator == ')' ? 'option' : 'question',
                'text' => $text,
                'data' => $separator == ')' ? str_replace($separator, '', $data) : $this->decodeAttribute($text, $data),
            ];
        }
    }

    protected function decodeAttribute($text, $attrData) {
        try {
            $flags = JSON_THROW_ON_ERROR | JSON_NUMERIC_CHECK;
            $attributes = json_decode($attrData, true, 512, $flags);
        } catch (\JsonException $e) {
            throw new \UnexpectedValueException("Attribute string '$attrData' in question '$text' is not valid JSON.", 1, $e);
        }
    }

    protected function createQuestion(array $structure) {
        return [
            'text' => $structure['text'],
            'type' => 'input'
        ];
    }

    protected function amendPreviousQuestion(& $questions, array $structure) {
        $text = $structure['text'];
        $index = count($questions) - 1;
        $questions[$index]['text'] .= ' ' . $text;
    }    

    protected function createOption(array $structure) {
        $text = trim($structure['text']);

        if (!empty($structure['data'])) {
            $key = $structure['data'];
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
