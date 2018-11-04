<?php
    namespace LINE2Discord;

    require_once 'ReadOnlyTrait.php';
    require_once 'Reader.php';

    use LINE2Discord\ReadOnlyTrait as ReadOnlyTrait;
    use LINE2Discord\Reader as Reader;
    
    class Settings
    {
        use ReadOnlyTrait;
        use Reader;

        private $configure = "configure.json";
        private $serviceURI;
        private $uploadURI;
        private $uploadLocation;
        private $defaultUserName;
        private $token;
        private $secret;
        private $groupId;
        private $userNameURL = "https://api.line.me/v2/bot/group/{roomId}/member/{userId}";
        private $downloadURL = "https://api.line.me/v2/bot/message/{messageID}/content";
        private $stickerURL = "https://stickershop.line-scdn.net/stickershop/v1/sticker/{stickerID}/ANDROID/sticker.png";
        private $discordUrl;
        private $botName;
        private $botThumbnail;
        private $maxFileSize;

        public function __construct()
        {
            // configureの読み込み
            $this->loadConfigure();
        }

        /**
         * configureファイルから設定を読み出します。
         *
         * @return void
         */
        private function loadConfigure()
        {
            // configure.jsonを読み込む
            $json = $this->readFileAsJson($this->configure);

            // URLの正しい解釈のためにsetlocaleを行う
            \setlocale(LC_ALL, $json["system"]["locale"]);
            $this->serviceURI = (empty($_SERVER['HTTPS']) ? "http://" : "https://") . $_SERVER['SERVER_NAME'] . \pathinfo($_SERVER['REQUEST_URI'])['dirname'] . DIRECTORY_SEPARATOR;
            $this->uploadURI = $this->serviceURI . $json["system"]["uploadLocation"] . DIRECTORY_SEPARATOR;
            $this->uploadLocation = __DIR__ . $json["system"]["uploadLocation"] . DIRECTORY_SEPARATOR;
            $this->defaultUserName = $json["system"]["defaultUserName"];

            $this->token = $json["line"]["token"];
            $this->secret = $json["line"]["secret"];
            $this->groupId = $json["line"]["groupId"];

            $this->discordUrl = $json["discord"]["hookUrl"];
            $this->botName = $json["discord"]["botName"];
            $this->botThumbnail = $json["discord"]["botThumbnail"];
            $this->maxFileSize = intval($json["discord"]["maxFileSize"]);
        }
    }
?>