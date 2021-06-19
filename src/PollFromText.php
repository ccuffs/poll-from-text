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

    protected function whatIsText($text) {
        $text = trim($text);

        if (empty($text)) {
            return 'empty';
        }

        $firstPart = '';
        $indexFirstSpace = stripos($text, ' ');

        if ($indexFirstSpace !== false) {
            $firstPart = substr($text, 0, $indexFirstSpace);
        }

        switch ($firstPart) {
            case '-': return 'option';
            default:  return 'question';
        }
    }

    protected function createQuestion($text) {
        return [
            'text' => $text,
            'type' => 'input'
        ];
    }

    protected function amendPreviousQuestion(& $questions, $text) {
        $index = count($questions) - 1;
        $questions[$index]['text'] .= ' ' . $text;
    }    

    protected function createOption($text) {
        $indexFirstSpace = stripos($text, ' ');

        if ($indexFirstSpace === false) {
            return $text;
        }

        return trim(substr($text, $indexFirstSpace + 1));
    }

    protected function addOptionToPreviousQuestion(& $questions, $text) {
        if (count($questions) == 0) {
            throw new \Exception('Unexepect option "- abc..." without preceding simple text "abc..."');
        }

        $index = count($questions) - 1;
        $questions[$index]['type'] = 'select';

        if (!isset($questions[$index]['options'])) {
            $questions[$index]['options'] = [];
        }

        $questions[$index]['options'][] = $this->createOption($text);
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

            $currentType = $this->whatIsText($currentLine);

            if ($currentType == 'question') {
                if($previousType == 'question' && @$config['mutliline_question']) {
                    $this->amendPreviousQuestion($questions, $currentLine);
                } else {
                    $questions[] = $this->createQuestion($currentLine);
                }
            } else if ($currentType == 'option') {
                $this->addOptionToPreviousQuestion($questions, $currentLine);
            }

            $previousType = $currentType;
        }

        return $questions;
    }
}
