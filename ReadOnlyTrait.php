<?php
    namespace LINE2Discord;

    trait ReadOnlyTrait
    {
        public function __get($name)
        {
            return $this->$name;
        }

        public function __isset($name)
        {
            return isset($this->$name);
        }
    }
?>