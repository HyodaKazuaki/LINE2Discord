<?php
    namespace LINE2Discord;

    require_once 'Writer.php';

    use LINE2Discord\Writer as Writer;

    class Logger
    {
        // ログのディレクトリ
        private $logDir;
        // ログのファイル名
        private $logFile;
        // Writerのインスタンス
        private $writer;
        // ログレベル
        private $logLevel = ['TRACE', 'DEBUG', 'INFO', 'WARN', 'ERROR', 'FATAL'];

        /**
         * __construct
         *
         * @param  mixed $dirName ディレクトリの位置
         * @param  mixed $fileName ログのファイル名
         *
         * @return void
         */
        public function __construct($dirName = __DIR__, $fileName = "log.txt")
        {
            $this->logDir = $dirName;
            $this->logFile = $fileName;

            $this->writer = new Writer($this->logDir . DIRECTORY_SEPARATOR . $this->logFile);

            $this->set();
        }

        /**
         * ログを保存します
         *
         * @param  mixed $level ログレベル
         * @param  mixed $message ログメッセージ
         *
         * @return void
         */
        public function log($level, $message)
        {
            // 時刻を取得
            $dateTime = date("Y-m-d H:i:s T");
            // 書き込むメッセージを構築
            $messageBuilder = $dateTime . " [" . $this->logLevel[$level] . "] " . $message . "\n";

            // 書き込み
            $this->writer->write($messageBuilder);
        }

        /**
         * エラー設定を行います
         *
         * @return void
         */
        private function set()
        {
            set_error_handler(function($error_no, $error_msg, $error_file, $error_line, $error_vars) {
                if (error_reporting() === 0) {
                    return;
                }
                throw new \ErrorException($error_msg, 0, $error_no, $error_file, $error_line);
            });
            
            set_exception_handler(function(\Throwable $throwable) {
                // 開発環境ならエラーログを標準出力出すようにしてしまったほうが使い勝手がよさそうです（なくてもいい）
                if (DEBUG === true) {
                    echo $throwable;
                }
                // 例外深刻度を変換する
                $level = 0;
                switch($throwable->getCode()){
                    case E_ERROR:
                    case E_CORE_ERROR:
                    case E_COMPILE_ERROR:
                        $level = 5;
                        break;
                    case E_USER_ERROR:
                    case E_RECOVERABLE_ERROR:
                    case E_PARSE:
                        $level = 4;
                        break;
                    case E_WARNING:
                    case E_CORE_WARNING:
                    case E_COMPILE_WARNING:
                    case E_USER_WARNING:
                        $level = 3;
                        break;
                    case E_NOTICE:
                    case E_USER_NOTICE:
                    case E_STRICT:
                    case E_DEPRECATED:
                    case E_USER_DEPRECATED:
                        $level = 2;
                        break;
                    default:
                        $level = 1;
                        break;
                }
                $this->log($level, $throwable->__toString());
            });

            register_shutdown_function(function() {
                $error = error_get_last();
                if ($error === null) {
                    return;
                }
                // fatal error の場合はすでに何らかの出力がされているはずなので、何もしない
                $throwable = new \ErrorException($error['message'], 0, 0, $error['file'], $error['line']);
                $this->log(1, $throwable->__toString());
            });
        }
    }
?>