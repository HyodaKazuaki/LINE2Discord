<?php
    namespace LINE2Discord;

    require_once 'ReadOnlyTrait.php';
    require_once 'Logger.php';
    require_once 'MIME.php';

    use LINE2Discord\ReadOnlyTrait as ReadOnlyTrait;
    use LINE2Discord\Logger as Logger;
    use LINE2Discord\MIME as MIME;

    class LineMessage
    {
        use ReadOnlyTrait;

        private $configure;
        private $logger;
        private $replyToken;
        private $messageId;
        private $sourceType;
        private $roomId;
        private $userId;
        private $userName;
        private $messageType;
        private $text;
        private $stickerId;
        private $fileName;
        private $fileSize;

        /**
         * __construct
         *
         * @param  mixed $configure Settingsのインスタンス
         * @param  mixed $logger Loggerのインスタンス
         *
         * @return void
         */
        public function __construct($configure, $logger)
        {
            $this->configure = $configure;
            $this->logger = $logger;
        }

        /**
         * JSONデータをロードします
         *
         * @param  mixed $decoded_json デコードしたJSON
         *
         * @return void
         */
        public function loadJson($decoded_json)
        {
            //if(DEBUG) return;
            $this->logger->log(0, \json_encode($decoded_json));
            $json = $decoded_json->{"events"}[0];
            
            $this->replyToken = $json->{"replyToken"};
            $this->messageId = $json->{"message"}->{"id"};
            $this->sourceType = $json->{"source"}->{"type"};
            $this->roomId = $this->sourceType == "user" ? $json->{"source"}->{"userId"} : $json->{"source"}->{"groupId"};
            $this->userId = $json->{"source"}->{"userId"};
            $this->messageType = $json->{"message"}->{"type"};
            $this->fileName = ($this->messageType == "file") ? $json->{"message"}->{"fileName"} : null;
            $this->fileSize = ($this->messageType == "file") ? $json->{"message"}->{"fileSize"} : null;

            $this->loadText($json);
            $this->loadStickerId($json);
            $this->getUserName();
        }

        /**
         * 存在する場合テキストをロードします
         *
         * @param  mixed $json デコードしたJSON
         *
         * @return void
         */
        private function loadText($json)
        {
            if($this->messageType == "text")
                $this->text = $json->{"message"}->{"text"};
        }

        /**
         * 存在する場合はステッカーをロードします
         *
         * @param  mixed $json デコードしたJSON
         *
         * @return void
         */
        private function loadStickerId($json)
        {
            if($this->messageType == "sticker")
                $this->stickerId = $json->{"stickerId"};
        }

        /**
         * ユーザー名をロードします
         *
         * @return void
         */
        private function getUserName()
        {
            $this->logger->log(0, "Get user name");
            $url = \str_replace("{userId}", $this->userId, \str_replace("{roomId}", $this->roomId, $this->configure->userNameURL));
            $headers = array(
                "Authorization: Bearer {$this->configure->token}",
            );
            $option = [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 3,
            ];
            $ch = curl_init($url);
            \curl_setopt_array($ch, $option);
            \curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            $json = \curl_exec($ch);
            $errno = \curl_errno($ch);
            $err = \curl_error($ch);
            $info = \curl_getinfo($ch);
            if($info['http_code'] != 200 || DEBUG){
                $this->logger->log(0, $url);
                $this->logger->log((DEBUG) ? 0 : 4 ,"{$err}, {$errno}({$info['http_code']})");
                $this->logger->log((DEBUG) ? 0 : 4 , "group: {$this->roomId}, user: {$this->userId}");
                $this->logger->log(0, $json);
            }
            $jsonObj = json_decode($json);
            $this->userName = ($info['http_code'] == 200) ? $jsonObj->{"displayName"} : "No name";
        }

        /**
         * ファイルをダウンロードし、ファイル名をロードします
         *
         * @return void
         */
        public function getFile()
        {
            $this->logger->log(0, "Get file");

            $tmp = \tmpfile();
            $tempName = stream_get_meta_data($tmp)['uri'];
            $url = str_replace("{messageID}", $this->messageId, $this->configure->downloadURL);
            $ch = \curl_init($url);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array("Authorization: Bearer {{$this->configure->token}}"));
            curl_setopt($ch, CURLOPT_FILE, $tmp);
            curl_setopt($ch, CURLOPT_FAILONERROR, true);
            if(!curl_exec($ch))
                $this->logger->log(4, \curl_error($ch) . \curl_errno($ch));
            switch ($this->messageType) {
                case 'file':
                    $fileName = $this->fileName;
                    break;
                case 'image':
                case 'audio':
                case 'video':
                    $mime = mime_content_type($tmp);
                    $fileName = $this->messageId . "." . MIME::TYPE[$mime];
                    break;
                default:
                    $this->logger->log(4, "Unknown message type {$this->messageType}");
                    return;
                    break;
            }
            \curl_close($ch);
            $this->logger->log(0, "Save file as {$fileName} on {$this->configure->uploadLocation}");
            \copy(stream_get_meta_data($tmp)['uri'], $this->configure->uploadLocation . $fileName);
            $this->fileName = $fileName;
        }

        /**
         * ファイルサイズがDiscordの許容サイズか判定します
         *
         * @return void
         */
        public function isFileOverSize()
        {
            return \filesize($this->configure->uploadLocation . $this->fileName) > $this->configure->maxFileSize;
        }
    }
?>