# EroTweet
System for creating a tweet queue with php and cronjobs<br/>
## How it Works
Input into a form the tweet you want to send. In the 4 image inputs place the images you like. Filesize is limited on both client and server to 5MB which is Twitter's limit. After confiming files are uploaded and paths+comment are stored in an SQL database with a PostNo primary key. Every hour, the server calls upon a posting script which takes the oldest/smallest PostNo post and sends it's info to be posted onto Twitter through PHP curl and the websites Media and Update API. It then deletes that entry and goes onto the next an hour later. If no data, the script just calls the function and Twitter.

<br/><br/>
Inspiration from no-you.com and https://github.com/mrbellek/twitterbot
