<?php
namespace KbizeCli\Console;

class String
{
    public function __construct($value)
    {
        $this->value = (string) $value;
    }

    public function fixed($size)
    {
        return new self(str_pad(
            substr($this->value, 0, $size),
            $size,
            ' '
        ));
    }

    public function color($color)
    {
        if ($color) {
            return new self("<fg=$color>$this->value</fg=$color>");
        }

        return $this;
    }

    public function __toString()
    {
        return $this->value;
    }
}
