<?php
    namespace LINE2Discord;
    define("DEBUG", false);

    require_once 'Settings.php';
    require_once 'LineMessage.php';
    require_once 'Logger.php';
    
    use LINE2Discord\Settings as Settings;
    use LINE2Discord\LineMessage as LineMessage;
    new Bot;

    class Bot
    {
        private $configure;
        private $lineMessage;
        private $logger;

        public function __construct()
        {
            $this->logger = new Logger();

            // 設定をロード
            $this->configure = new Settings();

            // jsonを受け取り
            $encoded_json = file_get_contents('php://input');
            
            // ハッシュチェック
            $this->logger->log(0, "Check hash");
            if(!$this->checkHash($encoded_json))
                return;

            // jsonをオブジェクトにする
            $this->logger->log(0, "Convert json to object");
            $decoded_json = json_decode($encoded_json)->{"events"};
            $this->logger->log(0, \json_encode($decoded_json, JSON_UNESCAPED_UNICODE));

            foreach($decoded_json as $json) {
                $this->lineMessage = new LineMessage($this->configure, $this->logger);
                $this->lineMessage->loadJson($json);
    
                // 特定のグループからの送信のみに絞る
                $this->logger->log(0, "Validation input");
                if(!$this->validation())
                    return;
    
                // 各処理へ振り分ける
                $this->logger->log(0, "Distribute each process");
                $this->distribute();
            }
        }

        private function checkHash($json)
        {
            if(DEBUG) return true;
            $hash = hash_hmac('sha256', $json, $this->configure->secret, true);
            $sig = base64_encode($hash);
            $header = \getallheaders();
            $this->logger->log(0, \json_encode($header, JSON_UNESCAPED_UNICODE));
            $compSig = $header['X-Line-Signature'];

            $this->logger->log(0, "SIG: {$sig}");
            $this->logger->log(0, "compSIG: {$compSig}");
            $this->logger->log(0, "All: " . var_export(\getallheaders()));

            if($sig != $compSig) {
                $this->logger->log(2, "Wrong hash");
                return false;
            }
            $this->logger->log(0, "Hash is correct");
            return true;
        }

        private function validation()
        {
            if(DEBUG) return true;
            if($this->lineMessage->sourceType == "group"
            && $this->lineMessage->roomId == $this->configure->groupId)
                return true;
            
            $this->logger->log(0, "Failed to validate");
            return false;
        }

        private function distribute()
        {
            switch ($this->lineMessage->messageType) {
                case 'image':
                case 'video':
                case 'audio':
                case 'file':
                    $this->lineMessage->getFile();
                case 'text':
                case 'sticker':
                    $this->sendMessage();
                    break;
                default:
                    $this->logger->log(2, "Something different message :" . $this->lineMessage->messageType);
                    break;
            }
        }

        private function sendMessage()
        {
            $this->logger->log(0, "Send message");

            $client = \curl_init($this->configure->discordUrl);

            \curl_setopt($client, CURLOPT_POST, true);
            \curl_setopt($client, CURLOPT_RETURNTRANSFER, true);
            \curl_setopt($client, CURLOPT_HTTPHEADER, array('Content-Type: multipart/form-data'));
            $name = (!empty($this->configure->botName)) ? array('username' => "{$this->lineMessage->userName} by {$this->configure->botName}") : array('username' => "{$this->lineMessage->userName}");
            $thumbnail = (!empty($this->configure->botThumbnail)) ? array('avatar_url' => $this->configure->botThumbnail) : array();
            $postfield = array('content' => 'Whoops, There is nothing.');
            switch ($this->lineMessage->messageType) {
                case 'text':
                    $postfield = array('content' => "{$this->lineMessage->text}");
                    break;
                case 'image':
                case 'video':
                case 'audio':
                case 'file':
                    // ファイルサイズチェック
                    if($this->lineMessage->isFileOverSize())
                        $postfield = array('content' => $this->configure->uploadURI . $this->lineMessage->fileName);
                    else{
                        $fileName = $this->configure->uploadLocation . $this->lineMessage->fileName;
                        $finfo = new \finfo(FILEINFO_MIME_TYPE);
                        $postfield = array('file' => new \CURLFILE($fileName, $finfo->file($fileName), $this->lineMessage->fileName));
                    }
                    break;
                /*case 'sticker':
                    // TODO: 動くステッカーや音声つきステッカーは別URL
                    $sticker = \str_replace("{stickerID}", $this->lineMessage->stickerId, $this->configure->stickerURL);
                    $postfield = array('content' => $sticker);
                    break;*/
                default:
                    $this->logger->log(4, "Unknown message type {$this->lineMessage->messageType}");
                    return;
                    break;
            }
            $field = \array_merge($name, $thumbnail, $postfield);
            \curl_setopt($client, CURLOPT_POSTFIELDS, $field);
            $this->logger->log(0, "Set webhook options");
            $this->logger->log(0, $field['avatar_url']);

            if(!\curl_exec($client)){
                $errorNo = \curl_errno($client);
                $error = \curl_error($client);
                $info = \curl_getinfo($client);
                if($info['http_code'] !== 204)
                    $this->logger->log(4, "Error sending to discord {$errorNo}({$info['http_code']}) {$error}");
            }
        }

    }
?>