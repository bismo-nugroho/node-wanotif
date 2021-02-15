### Forked from  <a href ='https://github.com/mhankbarbar/termux-wabot.git'>mhankbarbar/termux-wabot</a>

## Clone this project

```bash 
> git lone https://github.com/bismo-nugroho/node-wanotif.git
```

## Install the dependencies:
```bash 
npm install
```

### Usage
```bash
> npm start
```

### Settings

- index.js
```wa-notif
intervalcheck = 5; // 5 second
webHook = 'http://khataman.web/api'; <== your php api
```

- Backend
``` backend php
1. create yout database from wa.sql dump file

2. set your mysqldb
file coninfo.db
$hostdb = "localhost";
$userdb = "root";
$passdb = "";
$dbs = "wa";

3.  you can modify code in sendHook.php for any command that you want to response with

4. set your website path

- Info
if you want to change browser name in wa log change file :
nodemodules\@adiwajshing\baileys\lib\WAConnection/Utils.js
```
Line : 45 =>    baileys: browser => ['your broser name', browser, '18.04'],
```

## Features
1. Send notification to another WA contact using PHP API
2. Reply for some command eg: /appr for approve 


## Special Thanks to
* <a href="https://github.com/adiwajshing/Baileys">adiwajshing/baileys</a>
* <a href ='https://github.com/mhankbarbar/termux-wabot.git'>mhankbarbar/termux-wabot</a>

