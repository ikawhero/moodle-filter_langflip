<?php

/**
 * Langflip filtering
 *
 * Simply replaces latin characters with a unicode equivalent that looks like
 * an upside down version of the original. We also optionally reverse the
 * string so that it is readable upside down.
 *
 * @copyright 2010 Pukunui Technology
 * @author S.Elliott, Pukunui Technology
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package filter-langflip
 */


/**
 * Filter function
 *
 * @param integer $courseid  the current course id
 * @para string $text  the text to process
 * @return string
 */
function langflip_filter($courseid, $text) {
    global $CFG;

    if (!is_string($text)) {
        return $text;
    }

    $newtext = $text;
    
    $reverse = (!empty($CFG->filter_langflip_reverse));
    $flip = (!empty($CFG->filter_langflip_upsidedown));

    $newtext = flip_text($text, $reverse, $flip);

    if (is_null($newtext)) {
        $newtext = $text;
    }

    return $newtext;

}

/**
 * Recursive function to reverse text within a string. Most of the work here
 * is stripping out tags and then putting them back into the correct place
 *
 * @param string $text  the text to reverse
 * @param boolean $reverse  do we reverse the text
 * @param boolean $flip  do we turn do a unicode replace where possible
 * @return string
 */
function flip_text($text='', $reverse=false, $flip=false) {

    /// Define the unicode alternative characters
    static $alphabet = null;

    if (empty($alphabet)) {
        $latin = array( 'a' => 'ɐ',
                'b' => 'q',
                'c' => 'ɔ',
                'd' => 'p',
                'e' => 'ǝ',
                'f' => 'ɟ',
                'g' => 'ƃ',
                'h' => 'ɥ',
                'i' => 'ı',
                'j' => 'ɾ',
                'k' => 'ʞ',
                'l' => 'l',
                'm' => 'ɯ',
                'n' => 'u',
                'o' => 'o',
                'p' => 'd',
                'q' => 'b',
                'r' => 'ɹ',
                's' => 's',
                't' => 'ʇ',
                'u' => 'n',
                'v' => 'ʌ',
                'w' => 'ʍ',
                'x' => 'x',
                'y' => 'ʎ',
                'z' => 'z'
                    );

        $others = array( '!' => '¡',
                '?' => '¿',
                '(' => ')',
                ')' => '(',
                '{' => '}',
                '}' => '{',
                '[' => ']',
                ']' => '[',
                ';' => '؛',
                '\'' => ',',
                '.' => '˙'
                );

        $alphabet = $latin + $others;
    }

    /// Long tags
    $regex = "/<(([A-Za-z][A-Za-z0-9]*)\b[^>]*?)>(.*?)<\/\\2>/";
    $matches = array();
    $replace = array();

    /// Short tags
    $regex2 = "/(<[A-Za-z].*?\/>)/";
    $matches2 = array();
    $replace2 = array();

    /// HTML Entities
    $regex3 = "/(\&[A-Za-z0-9]+?;)/";
    $matches3 = array();
    $replace3 = array();


    /// Let's break the original text apart
    preg_match_all($regex, $text, $matches, PREG_SET_ORDER);
    foreach ($matches as $key=>$match) {
        $longkey = sprintf("%03d", $key);
        $revlongkey = strrev($longkey);
        $replace[$key] = '$$$'.$longkey.'-'.$revlongkey.'$$$';
        $text = str_replace($match[0], $replace[$key], $text);
    }

    preg_match_all($regex2, $text, $matches2, PREG_SET_ORDER);
    foreach ($matches2 as $key=>$match) {
        $longkey = sprintf("%03d", $key);
        $revlongkey = strrev($longkey);
        $replace2[$key] = '$$#'.$longkey.'-'.$revlongkey.'#$$';
        $text = str_replace($match[0], $replace2[$key], $text);
    }

    preg_match_all($regex3, $text, $matches3, PREG_SET_ORDER);
    foreach ($matches3 as $key=>$match) {
        $longkey = sprintf("%03d", $key);
        $revlongkey = strrev($longkey);
        $replace3[$key] = '$#$'.$longkey.'-'.$revlongkey.'$#$';
        $text = str_replace($match[0], $replace3[$key], $text);
    }

    /// Reverse text
    if ($reverse) {
        $text = strrev($text);
    }

    /// Flip it over
    if ($flip) {
        $text = strtr(moodle_strtolower($text), $alphabet);
    }


    /// And now let's put the pieces back together
    foreach ($matches3 as $key=>$match) {
        $text = str_replace($replace3[$key], $match[0], $text);
    }

    foreach ($matches2 as $key=>$match) {
        $text = str_replace($replace2[$key], $match[0], $text);
    }

    foreach ($matches as $key=>$match) {
        $starttag = '<'.$match[1].'>';
        $endtag = '</'.$match[2].'>';
        $rtext = flip_text($match[3], $reverse, $flip);
        $text = str_replace($replace[$key], $starttag.$rtext.$endtag, $text);
    }

    return $text;
}

?>
