<?php

class HTMLPurifier_AttrDef_HTML5_InputType extends HTMLPurifier_AttrDef
{
    /**
     * Lookup table for valid values
     * @var array
     */
    protected static $values = array(
        'hidden' => true,
        'text' => true,
        'search' => true,
        'tel' => true,
        'url' => true,
        'email' => true,
        'password' => true,
        'date' => true,
        'month' => true,
        'week' => true,
        'time' => true,
        'datetime-local' => true,
        'number' => true,
        'range' => true,
        'color' => true,
        'checkbox' => true,
        'radio' => true,
        'file' => true,
        'submit' => true,
        'image' => true,
        'reset' => true,
        'button' => true,
    );

    /**
     * @var array
     */
    protected $allowed;

    /**
     * Return lookup table for valid 'type' values
     *
     * @return array
     * @codeCoverageIgnore
     */
    public static function values()
    {
        return self::$values;
    }

    /**
     * @param string $string
     * @param HTMLPurifier_Config $config
     * @param HTMLPurifier_Context $context
     * @return bool|string
     */
    public function validate($string, $config, $context)
    {
        if ($this->allowed === null) {
            $allowedInputTypes = isset($config->def->info['Attr.AllowedInputTypes'])
                ? (array) $config->get('Attr.AllowedInputTypes')
                : array();

            if (empty($allowedInputTypes)) {
                $allowed = self::$values;
            } else {
                $allowed = array_intersect_key($allowedInputTypes, self::$values);
            }
            $this->allowed = $allowed;
        }

        $type = strtolower($this->parseCDATA($string));

        if ($type === '') {
            $type = 'text';
        }

        // The datetime input type field has been removed from WHATWG HTML and replaced
        // with datetime-local, see: https://github.com/whatwg/html/issues/336
        if ($type === 'datetime') {
            $type = 'datetime-local';
        }

        if (!isset($this->allowed[$type])) {
            return false;
        }

        return $type;
    }
}
