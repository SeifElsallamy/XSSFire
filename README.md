# XSSFire
A standalone Blind XSS Script.

How it works:
The script is a combination between PHP and JavaScript, it captures the data using JS and send it to the script itself, then it generates report files and deliver them through telegram. 

#Requirements:
1. Web Server running on Linux with a domain and an SSL certificate.
2. Telegram account (To receive BXSS reports)

#Why Telegram?
Easy to setup.
Fast.
Reachable.

#Features:
1. Captures: IP, User-Agent, Date-Time, Local Storage, Session Storage, Cookies, Referrer, Origin, URL, HTML, If fired from an Iframe  
2. Session Builder: A JavaScript that can rebuild the session, open the captured URL and the devtools then paste it in the console and hit enter. If you're luck enough you can takeover the session.
3. Telegram Notifications: Faster and easier to setup than e-mails and more reachable.
4. Anti-Spam: You should only receive one/two notification for each captured URL. However, the script resets every once in a while to notify you again later.
5. Encryption: You can set a password in the script and the report you receive in telegram will be a zip file protected by a password. (Telegram bots are not using end-to-end encryption).
6. HTML Snapshot: Is an HTML file that will be delivered with the report, it should look similar to the page of your target.

#Setup:
1. Go to telegram and have a chat with the @botfather.
2. Write /start in the chat and create new bot.
3. You should receive your bot token when you create it.
4. Save the token aside.
5. Now go to your newly created bot chat and start it then say hi.
6. Then go to: https://api.telegram.org/bot<YourBOTToken>/getUpdates
7. And save your chat id.
8. Open the XSSFire Script and modify the following lines:
```
$chat_id = "<Telegram Chat ID>"; // Your Telegram Chat ID
$token = "<Telegram Bot Access Token>"; // Your telegram Bot Access token
$password = False; //Protect the Blind XSS Report with a password. Leave as False if not used.
```
9. Upload the script to your server.

To generate payloads you might consider using the Blind-XSS-Manager https://github.com/SeifElsallamy/Blind-XSS-Manager
You can simply create payloads through this extension by entering yourdomain.com/XSSFire.js in the domain field and click Save.

#Snapshots:
<img width="578" alt="bxss" src="https://user-images.githubusercontent.com/11223632/210347560-fb24b4fb-9927-4973-8a39-42802c308601.png">
<img width="278" alt="bxss2" src="https://user-images.githubusercontent.com/11223632/210348003-c4d49ac0-62ea-4fca-8b4b-c2247bdd1c1d.png">


 

