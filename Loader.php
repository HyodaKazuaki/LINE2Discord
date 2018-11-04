<?php
    namespace LINE2Discord;

    require_once 'Settings.php';

    use LINE2Discord\Settings as Settings;

    class Loader
    {
        private $_configure = "configure.json";
        private $settings;

        /**
         * __construct
         *
         * @param  Settings $settingsのポインタ
         *
         * @return void
         */
        public function __construct(Settings &$settings)
        {
            $this->settings = &$settings;
        }

        /**
         * configure.jsonから設定を読み出します。
         *
         * @return void
         */
        public function loadConfigure()
        {
            // configure.jsonを読み込む
            $json = $this->loadConfigureAsJson();

            $settings->_token = $json["line"]["token"];
            $settings->_secret = $json["line"]["secret"];
            $settings->_groupId = $json["line"]["groupId"];

            $settings->_maxFileSize = intval($json["system"]["maxFileSize"]);
            $settings->_discordUrl = $json["discord"]["hookUrl"];
        }

        /**
         * configure.jsonをJSON形式で読み込みます。
         *
         * @return array JSONデータ
         */
        private function loadConfigureAsJson()
        {
            $json = file_get_contents($this->_configure);
            $json = mb_convert_encoding($json, 'UTF8', 'ASCII,JIS,UTF-8,EUC-JP,SJIS-WIN');
            return json_decode($json, true);
        }
    }
?>