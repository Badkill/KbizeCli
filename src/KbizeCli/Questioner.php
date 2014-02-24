<?php
namespace KbizeCli;

interface Questioner
{
    public function ask($question, $default = '', $hiddenResponse = false);
}
