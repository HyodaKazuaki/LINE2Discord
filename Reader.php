<?php
    namespace LINE2Discord;

    trait Reader
    {
        /**
         * ファイルをJSON形式で読み込みます。
         *
         * @param  mixed 読み込むファイル
         *
         * @return array JSONデータ
         */
        private function readFileAsJson($file)
        {
            $json = file_get_contents($file);
            $json = mb_convert_encoding($json, 'UTF8', 'ASCII,JIS,UTF-8,EUC-JP,SJIS-WIN');
            return json_decode($json, true);
        }
    }
?>