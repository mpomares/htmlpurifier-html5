#!/usr/bin/php
<?php

// Error message from https://validator.nu/ for <input type="text" min="0" />
// used for extracting map of allowed input attributes depending on input type
$input = '
accept	when type is file
alt	when type is image
autocomplete	when type is text, search, url, tel, email, password, date, month, week, time, datetime-local, number, range, or color
autofocus
checked	when type is checkbox or radio
dirname	when type is text or search
disabled
form
formaction	when type is submit or image
formenctype	when type is submit or image
formmethod	when type is submit or image
formnovalidate	when type is submit or image
formtarget	when type is submit or image
height	when type is image
list	when type is text, search, url, tel, email, date, month, week, time, datetime-local, number, range, or color
max	when type is date, month, week, time, datetime-local, number, or range
maxlength	when type is text, search, url, tel, email, or password
min	when type is date, month, week, time, datetime-local, number, or range
multiple	when type is email or file
name
pattern	when type is text, search, url, tel, email, or password
placeholder	when type is text, search, url, tel, email, password, or number
readonly	when type is text, search, url, tel, email, password, date, month, week, time, datetime-local, or number
required	when type is text, search, url, tel, email, password, date, month, week, time, datetime-local, number, checkbox, radio, or file
size	when type is text, search, url, tel, email, or password
src	when type is image
step	when type is date, month, week, time, datetime-local, number, or range
type
value	when type is not file or image
width	when type is image
';

// https://html.spec.whatwg.org/dev/input.html#attr-input-type
$valid_types = '
hidden	Hidden	An arbitrary string	n/a
text	Text	Text with no line breaks	A text control
search	Search	Text with no line breaks	Search control
tel	Telephone	Text with no line breaks	A text control
url	URL	An absolute URL	A text control
email	E-mail	An e-mail address or list of e-mail addresses	A text control
password	Password	Text with no line breaks (sensitive information)	A text control that obscures data entry
date	Date	A date (year, month, day) with no time zone	A date control
month	Month	A date consisting of a year and a month with no time zone	A month control
week	Week	A date consisting of a week-year number and a week number with no time zone	A week control
time	Time	A time (hour, minute, seconds, fractional seconds) with no time zone	A time control
datetime-local	Local Date and Time	A date and time (year, month, day, hour, minute, second, fraction of a second) with no time zone	A date and time control
number	Number	A numerical value	A text control or spinner control
range	Range	A numerical value, with the extra semantic that the exact value is not important	A slider control or similar
color	Color	An sRGB color with 8-bit red, green, and blue components	A color picker
checkbox	Checkbox	A set of zero or more values from a predefined list	A checkbox
radio	Radio Button	An enumerated value	A radio button
file	File Upload	Zero or more files each with a MIME type and optionally a file name	A label and a button
submit	Submit Button	An enumerated value, with the extra semantic that it must be the last value selected and initiates form submission	A button
image	Image Button	A coordinate, relative to a particular image\'s size, with the extra semantic that it must be the last value selected and initiates form submission	Either a clickable image, or a button
reset	Reset Button	n/a	A button
button	Button	n/a	A button
';

$all_types = array();
foreach (extract_rows($valid_types) as $line) {
    list($type, ) = explode("\t", $line);
    $all_types[$type] = true;
}

$allowed = array();
foreach (extract_rows($input) as $line) {
    list($attr, $types) = explode("\t", $line, 2);

    $types = str_replace('when type is ', '', $types);
    $types = str_replace(', or ', ', ', $types);
    $types = str_replace(' or ', ', ', $types);
    $types = trim($types);

    $invert = false;
    if (substr($types, 0, 4) === 'not ') {
        $invert = true;
        $types = substr($types, 4);
    }

    $types = preg_split('/\s*,\s*/', $types);
    $types = array_map(function () { return true; }, array_flip($types));

    if ($invert) {
        $a = $all_types;
        foreach ($types as $type => $_) {
            unset($a[$type]);
        }
        $types = $a;
    }

    $allowed[$attr] = $types;
}

$result = var_export($allowed, true);
$result = str_replace('array (', 'array(', $result);
$result = preg_replace('/=>\s*array/', '=> array', $result);

echo $result, "\n\n";

function extract_rows($input) {
    $input = str_replace("\r\n", "\n", $input);
    $input = trim($input);
    $input = explode("\n", $input);
    $rows = array();
    foreach ($input as $row) {
        if (strpos($row, "\t") === false) {
            continue;
        }
        $rows[] = $row;
    }
    return $rows;
}

