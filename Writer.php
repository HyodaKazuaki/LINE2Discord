<?php
    namespace LINE2Discord;

    class Writer
    {
        // 書き込むファイル
        private $File;

        // アクセス形式
        private $mode;

        // ファイルハンドル
        private $fileHandle;

        public function __construct($fileName)
        {
            $this->File = $fileName;
        }

        public function write($message)
        {
            file_put_contents($this->File, $message, FILE_APPEND | LOCK_EX);
        }

    }
?>