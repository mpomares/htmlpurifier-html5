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

$input = trim(str_replace("\r\n", "\n", $input));
$input = explode("\n", $input);

$allowed = array();
foreach ($input as $line) {
    if (strpos($line, "\t") === false) {
        // Attribute is allowed in all types, skip
        continue;
    }
    list($attr, $types) = explode("\t", $line, 2);
    $types = str_replace('when type is ', '', $types);
    $types = str_replace(', or ', ', ', $types);
    $types = str_replace(' or ', ', ', $types);
    $types = preg_split('/\s*,\s*/', $types);

    $allowed[$attr] = array_map(function () { return true; }, array_flip($types));
}

$result = var_export($allowed, true);
$result = str_replace('array (', 'array(', $result);
$result = preg_replace('/=>\s*array/', '=> array', $result);

echo $result;
