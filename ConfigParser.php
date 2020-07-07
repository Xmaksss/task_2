<?php

/**
 * Class Parser
 */
class ConfigParser
{
    /**
     * @var string
     */
    private $_path;

    /**
     * Parser constructor.
     * @param string $path
     */
    public function __construct(string $path)
    {
        $this->_path = $path;
    }

    public function parse()
    {
        if (!file_exists($this->_path))
            throw new \Exception("File doesn't exist: $this->_path");

        $content = file_get_contents($this->_path);

        $lines = explode("\n", $content);

        $config = [];

        foreach ($lines as $line) {
            $line = trim($line);

            preg_match('~^(?P<keys>[a-z._0-9]+)\s?\=\s?(?P<value>\"?.*\"?)~', $line, $matches);

            if (empty($matches)) continue;

            $this->setValue($config, $matches['keys'], $matches['value']);
        }

        return $config;
    }

    private function setValue(array &$config, string $keys, $value)
    {
        $keys = explode('.', $keys);

        $array = [];

        for ($i = (count($keys) - 1); $i >= 0; $i--) {
            if ($i == (count($keys) - 1)) {
                $array[$keys[$i]] = $this->parseValue($value);
            } else {
                $newArray = [];
                $newArray[$keys[$i]] = $array;
                $array = $newArray;
            }
        }

        $config = array_merge_recursive($config, $array);
    }

    private function parseValue($value)
    {
        if (strtolower($value) == 'true') {
            return true;
        } else if (strtolower($value) == 'false') {
            return false;
        } else if (is_string($value) && $value[0] == '"' && $value[strlen($value) - 1] == '"') {
            return trim($value, '"');
        } else if ($value == (int)$value) {
            return (int)$value;
        }

        return $value;
    }
}