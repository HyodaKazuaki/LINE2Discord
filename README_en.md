LINE2Discord Bot
=====
日本語のREADMEは`README.md`です。

## What is this
This bot send messages to group on Discord from group on LINE.
Sendable/Receivable messages are below.
* text messages
* images
* audio files
* movies
* other files

bot **cannot** send stickers and location information now.

## Getting Started
1. Generate Discord Webhook
2. Login LINE Developers
3. Setting configuration file
4. Upload files
5. Setting LINE Bot
### Generate Discord Webhook
Generate Discord Webhook. You open server settings where server what you want to add bot. And you select Webhook, generate Webhook. After you setting Webhook, copy `WEBHOOK URL`.

### Login LINE Developers
Login [LINE Developers](https://developers.line.biz/). If you don't have a LINE Developers account, you register it. It's OK you register it on free account.
After login, you make a new bot, and copy `access token` and `secret key`.

And also **you get group's ID(`group id`) what you want to use this bot.**

### Setting configuration file
You clone this repository, rename `configure.json.sample` to `configure.json`. After that, you paste `WEBHOOK URL` to `hookUrl`, `access token` to `token`, `secret key` to `secret`, `group ID` to `groupId`. About configuration details and other settings, reference [Configuration].

### Upload files
You need a web server that available to use PHP 7 or over, and HTTPS. You upload files that are program, configuration file to the directory which you want to access on the server.

### Setting LINE Bot
View LINE Developers page and paste link which to `bot.php` that you uploaded on the server to `Webhook URL` column.

Finally, you invite the bot to a group from that you want to get messages.

## Configuration
These below are setting items for `configure.json.sample`. Please rename `configure.json.sample` to `configure.json` if you setting configuration.

| Group | Item | Description |
| ---- | ---- | ---- |
| system | locale | [Locale setting for PHP](http://php.net/manual/en/function.setlocale.php). Please set as required. |
| system | defaultUserName | Default name used to Discord. If line user don't accept Clause on the use of personal information, this name will be used. |
| system | uploadLocation | Place where files sent from LINE group will be downloaded. The path is relative path from `bot.php`. |
| line | token | Access token which needs to access LINE Service. |
| line | secret | Secret key which needs to access LINE Service. |
| line | groupId | LINE group id to limit messages from other groups. |
| discord | hookUrl | Discord's WEBHOOK URL.|
| discord | botName | Bot name that display on Discord. |
| discord | botThumbnail | Bot thumbnail that display on Discord. |
| discord | maxFileSize | Max file size(byte) that the bot can send to Discord. The messages will be send to Discord as file link if the file size is over this setting size. |

## Notes
This bot download files from LINE Group, but these files do not deleted. And also log file('log.txt`) will be get bloated, so you have to delete these files if neccesary.

## LICENSE
This programs are released under the MIT LICENSE. Please read `LICENSE.md`.