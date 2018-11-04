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

        /**
         * __construct
         *
         * @param  mixed $fileName ファイル名
         *
         * @return void
         */
        public function __construct($fileName)
        {
            $this->File = $fileName;
        }

        /**
         * ファイルに書き込みます
         *
         * @param  mixed $message 書き込む内容
         *
         * @return void
         */
        public function write($message)
        {
            file_put_contents($this->File, $message, FILE_APPEND | LOCK_EX);
        }

    }
?>