# LINE2Discord Bot
English README is working progress! Hold a while!

## できること
LINEのグループに送信されたメッセージをDiscordのグループへ送信します。
送信できるメッセージは次の通りです。
* テキストメッセージ
* 画像
* 音声
* 動画
* ファイル

スタンプや位置情報については送信できません。

## 導入方法
### Discord Webhookの用意
Discordの該当チャンネルの編集を押し、WebhooksにあるWebhookを作成からWebhookを作成してください。
各種Webhookの設定を済ませたら、WEBHOOK URLをコピーしておいてください。

### LINE Developersへの登録
はじめにLINEでBotを利用するために、[LINE Developers](https://developers.line.me/ja/)に登録します。メッセージの送信を行わないので、Free版で登録していただいて構いません。  
Botを作成したら、アクセストークンとシークレットキーをコピーしておいてください。

また、**Botを利用するLINEのグループのグループIDを取得しておいてください。**

### Botの設定
このリポジトリからファイルをダウンロードして、``configure.json.sample``を``configure.json``にリネームし、先程コピーしたDiscordのWEBHOOK URL、LINEのアクセストークン、シークレットキーとグループIDをhookUrl、token、secret、groupIdの該当箇所に貼り付けてください。詳細やその他の設定項目については、下にある``設定について``の項目で説明しています。  

### サーバの用意
このBotを稼働させるサーバを用意してください。
PHP7以上が動作し、**HTTPS通信ができる必要があります。**
先程ダウンロードして設定したファイル群をまとめてサーバの該当箇所にアップロードします。

### LINE Botの設定
再びLINE DevelopersのBotページに戻り、Webhook URLに先程アップロードしたファイルのうちの``bot.php``のアドレスを入力します。正しくアクセスできれば自動的に設定が完了します。  
次に、LINEアプリ側でBotを該当のグループに招待し、作業は完了です。

## 設定について
``configure.json.sample``の設定について記載しています。実際に使用する場合は``configure.json``にリネームしてください。

| グループ | 項目名 | 説明 |
|---|---|---|
| system | locale | [PHPの言語設定](http://php.net/manual/ja/function.setlocale.php)です。マルチバイトドメインの処理に影響があることがあるため必要に応じて設定してください。 |
| system | defaultUserName | Discordに表示されるデフォルトのユーザー名です。LINEの個人情報利用に関する条項に同意していない場合はユーザー名が取得できないため、この名前が使われます。 |
| system | uploadLocation | LINEから送信されたファイルが保存される位置です。これらファイルがある位置をカレントディレクトリとしています。 |
| line | token | LINEのBotで必要となるアクセストークンです。 |
| line | secret | LINEのBotで必要となるシークレットキーです。 |
| line | groupId | 受信するグループを限定するためのグループIDです。 |
|||
| discord | hookUrl | DiscordのWEBHOOK URLです。|
| discord | botName | Discordのチャットで表示されるBotの名前です。 |
| discord | botThumbnail | Discordのチャットで表示されるBotのサムネイル画像のURLです。 |
| discord | maxFileSize | Discordに送信できる最大のファイルサイズです。バイト形式で入力してあり、これを超える大きさのファイルはURLがメッセージとして送信されます。 |
|||

## 注意事項
このBotはLINEのグループからファイルをダウンロードしますが、削除しないためサーバ上に残り続けます。  
また、log.txtも非常に肥大化するため長期間の使用時は定期的にこれらファイルを削除してください。

## 今後の予定
* ログ関係の整備
* デバッグ関係の改善
* スタンプの対応
位置情報については対応予定はありません。もし熱い要望があれば検討します。

## ライセンス
このプログラムはMITライセンスの元、配布されます。ライセンスについてはLICENSE.mdをご覧ください。