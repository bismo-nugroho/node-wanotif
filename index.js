const {
	WAConnection,
	MessageType,
	Presence,
	Mimetype,
	GroupSettingChange
} = require('@adiwajshing/baileys')


var arrmsgs = [];
const { color, bgcolor } = require('./lib/color')
const { wait, simih, getBuffer, h2k, generateMessageID, getGroupAdmins, getRandom, banner, start, info, success, close } = require('./lib/functions')
const { fetchJson, fetchText } = require('./lib/fetcher')
//const { recognize } = require('./lib/ocr')
const fs = require('fs')
const intervalcheck = 5; // 5 second
const webHook = 'http://khataman.web/api';
const moment = require('moment-timezone')
const { exec } = require('child_process')
const fetch = require('node-fetch')
const { removeBackgroundFromImageFile } = require('remove.bg')

const { text, extendedText, contact, location, liveLocation, image, video, sticker, document, audio, product } = MessageType

prefix = '.'
blocked = []
var elapsed = 0;
var counts = -1;

function sleeps(time) {
	//promise sleep
	return new Promise((resolve) => setTimeout(resolve, time));
}

function checkElapsed() {
	if (elapsed >= 10) {

	}
}

function getAck(stats) {
	//ack map

	switch (stats) {
		case 'SERVER_ACK':
			return 1;
			break;
		case 'DELIVERY_ACK':
			return 2;
			break;
		case 'READ_ACK':
			return 3;
		case 'ERROR':
			return 9;

	}

}


async function checkContact(client, dest) {
	//check contact if exist on current chat
	var len = 0;
	const messages = await client.loadMessages(dest, 1);
	//console.log("msgs=",messages[0]);
	var msg = JSON.stringify(messages);
	msg = JSON.parse(msg);
	len = msg.messages.length;
	if (len > 0) return true;

	return false;
}

async function checkNewMessage(client) {
	//check new message from database if found
	console.log('Check Message Routine...');
	data = {};
	try {
		var voss = await fetchText(webHook + '/getHook.php');
		//console.log("response from checkMessage = " + voss);
		data = JSON.parse(voss);
		console.log("response from checkMessage = " + voss);
	} catch (e) {
		data.status = "false";
		console.log('Error : %s', color(e, 'red'))
	}

	if (data.status === "true") {
		var dests = data.dest + "@c.us";
		const isExists = await checkContact(client, dests);

		//const messages = await client.loadMessages (dests, 1);
		// console.log("got back from chatid = "+dests + "=" + messages + " messages",messages);

		if (isExists) {
			var dest = data.dest + "@s.whatsapp.net";
			data.idchat = dest;
			sendMessageText(client, dest, data.message, text);
			arrmsgs.push(data);
		} else {
			const exists = await client.isOnWhatsApp(data.dest);
			console.log("existed=", exists);
			if (exists) {
				//contact is found
				data.idchat = exists.jid;
				sendMessageText(client, dest, data.message, text);
				arrmsgs.push(data);
			} else {
				//contact not found
				var chat = {};
				chat.key = {};
				var msg = data;
				chat.status = 'ERROR';
				chat.key.id = 0;
				data.idchat = "not found";
				//console.log('body match');
				msg.chat_id = chat.key.id;
				msg.ack = 0;
				var ms = JSON.stringify(msg);
				updateChatStatus(client, chat, msg);
			}
		}
	} else {

		sleeps(intervalcheck * 1000).then(async () => {
			await checkNewMessage(client);
			//	console.log('Check Message Routine...');
		});
	}
}


async function ticks() {
	//just ticks
	counts++;
	sleeps(1000).then(async () => {
		await ticks();
		//	console.log('Check Message Routine...');
	});
}


function sendMessageText(client, from, teks, text) {
	//send message , typing first, wait for few seconds and send (looks like a human)
	const froms = from;
	client.updatePresence(from, Presence.composing) // tell them we're composing
	sleeps(3000).then(() => {
		client.sendMessage(from, teks, text);
	});
}


async function updateChatStatus(client, chat, msg) {
	//update status , update to database that message has been sent
	console.log('body match');
	msg.chat_id = chat.key.id;
	msg.ack = getAck(chat.status);
	var ms = JSON.stringify(msg);
	console.log("encoded=", ms);
	ms = encodeURIComponent(ms)
	var res = await fetchText(webHook + '/updateHook.php?source=' + ms);
	console.log('result=', res);
	counts = 0;
	sleeps(intervalcheck * 1000).then(async () => {
		await checkNewMessage(client);
		//	console.log('Check Message Routine...');
	});
}

async function starts() {
	const client = new WAConnection()
	client.logger.level = 'warn'
	console.log(banner.string)
	//first time
	client.on('qr', () => {
		console.log(color('[', 'white'), color('!', 'red'), color(']', 'white'), color(' Scan the qr code above'))
	})
    
	//if credential log has been found, connect without qr
	fs.existsSync('./Credential.json') && client.loadAuthInfo('./Credential.json')
	client.on('connecting', () => {
		start('2', 'Connecting...')
	})

	//connected successfully
	client.on('open', () => {
		arrmsgs = [];
		success('2', 'Connected')
		//const time = moment.tz('Asia/Jakarta').format('YYYY-MM-DD HH:mm:ss')
		//sendMessageText(client,'6289635866667@s.whatsapp.net',"wa initilised -"+time,text);
		if (counts == -1) {
			ticks();
			sleeps(intervalcheck * 1000).then(async () => {
				await checkNewMessage(client);
				//console.log('Check Message Routine...');
			});
		}

		counts = 0;
	})

	await client.connect({ timeoutMs: 30 * 1000 })
	fs.writeFileSync('./Credential.json', JSON.stringify(client.base64EncodedAuthInfo(), null, '\t'))

	client.on('CB:Blocklist', json => {
		if (blocked.length > 2) return
		for (let i of json[1].blocklist) {
			blocked.push(i.replace('c.us', 's.whatsapp.net'))
		}
	})

	client.on('chats-update', async (chat) => {
		console.log("check staus updating ");
		try {
			chats = JSON.stringify(chat);
			chats = JSON.parse(chats);
			console.log(chats);
			for (x = 0; x < chats.length; x++) {
				var msg = JSON.stringify(chats[x].messages)
				var msgs = JSON.parse(msg);
				console.log(msgs);
			}

			//console.log("chat-update=",JSON.stringify(chat));
			//  console.log("statuss=",chats.status);
		} catch (e) {
			console.log('Error : %s', color(e, 'red'))
		}
	})

	client.on('chats-received', async (chat) => {
		console.log("chat-received =", JSON.stringify(chat));

	});

	client.on('chat-update', async (chat) => {
		try {
			console.log("chat-update =", JSON.stringify(chat));
			chats = JSON.parse(JSON.stringify(chat));

			if (!chat.hasNewMessage) return
			chat = JSON.parse(JSON.stringify(chat)).messages[0]
			if (!chat.message) return

			if (chat.key && chat.key.remoteJid == 'status@broadcast') return

			//body = (type === 'conversation' && chat.message.conversation) ? chat.message.conversation : (type == 'imageMessage') && chat.message.imageMessage.caption.startsWith(prefix) ? chat.message.imageMessage.caption : (type == 'videoMessage') && chat.message.videoMessage.caption.startsWith(prefix) ? chat.message.videoMessage.caption : (type == 'extendedTextMessage') && chat.message.extendedTextMessage.text.startsWith(prefix) ? chat.message.extendedTextMessage.text : ''
			//budy = (type === 'conversation') ? chat.message.conversation : (type === 'extendedTextMessage') ? chat.message.extendedTextMessage.text : ''


			if (chat.key.fromMe && arrmsgs.length > 0) {
				//check message status sent
				status = chat.status;
				var arrs = arrmsgs.slice();
				arrs = arrs.reverse();
				var msg = arrs.pop();
				var body = chat.message.extendedTextMessage.text;
				console.log("compare= ", body, "==", msg.message);
				if (msg.message === body) {
					arrmsgs = arrs.slice();
					await updateChatStatus(client, chat, msg);
				} else {
					console.log('body not match');
					//arrmsgs.push(msg);
				}
			}

			if (chat.key.fromMe) return
			global.prefix
			global.blocked
			const content = JSON.stringify(chat.message)
			const from = chat.key.remoteJid
			const type = Object.keys(chat.message)[0]

			console.log("content=", content);
			console.log("from=", from);
			console.log("types=", type);
			const apiKey = 'Your Api Key' // contact me on whatsapp wa.me/6285892766102
			//const { text, extendedText, contact, location, liveLocation, image, video, sticker, document, audio, product } = MessageType
			const time = moment.tz('Asia/Jakarta').format('DD/MM HH:mm:ss')
			body = (type === 'conversation' && chat.message.conversation) ? chat.message.conversation : (type == 'imageMessage') && chat.message.imageMessage.caption.startsWith(prefix) ? chat.message.imageMessage.caption : (type == 'videoMessage') && chat.message.videoMessage.caption.startsWith(prefix) ? chat.message.videoMessage.caption : (type == 'extendedTextMessage') && chat.message.extendedTextMessage.text.startsWith(prefix) ? chat.message.extendedTextMessage.text : ''
			budy = (type === 'conversation') ? chat.message.conversation : (type === 'extendedTextMessage') ? chat.message.extendedTextMessage.text : ''
			const command = body.slice(1).trim().split(/ +/).shift().toLowerCase()
			const args = body.trim().split(/ +/).slice(1)
			const isCmd = body.startsWith(prefix)
			console.log("body:", body);
			console.log("command:", command);

			console.log('wait 4 seconds, chat hase new message = ', chat.hasNewMessage);
			if (!chats.hasNewMessage) {
				if (chats.messages) {
					console.log('updated message: ', chats.messages.first)
				}
				return
			}
			console.log('wait 4 seconds');
			setTimeout(async () => {
				sleeps(35000).then(() => {
					client.updatePresence(from, Presence.unavailable) // tell them we're available
				});
				await client.chatRead(from) // mark chat read
				await client.updatePresence(from, Presence.available) // tell them we're available
			}, 4 * 1000)


			mess = {
				wait: '⌛ Sedang di Prosess ⌛',
				success: '✔️ Berhasil ✔️',
				error: {
					stick: '❌ Gagal, terjadi kesalahan saat mengkonversi gambar ke sticker ❌',
					Iv: '❌ Link tidak valid ❌'
				},
				only: {
					group: '❌ Perintah ini hanya bisa di gunakan dalam group! ❌',
					ownerG: '❌ Perintah ini hanya bisa di gunakan oleh owner group! ❌',
					ownerB: '❌ Perintah ini hanya bisa di gunakan oleh owner bot! ❌',
					admin: '❌ Perintah ini hanya bisa di gunakan oleh admin group! ❌',
					Badmin: '❌ Perintah ini hanya bisa di gunakan ketika bot menjadi admin! ❌'
				}
			}

			const botNumber = client.user.jid
			const ownerNumber = ["628@s.whatsapp.net"] // replace this with your number
			const isGroup = from.endsWith('@g.us')
			const sender = isGroup ? chat.participant : chat.key.remoteJid
			const groupMetadata = isGroup ? await client.groupMetadata(from) : ''
			const groupName = isGroup ? groupMetadata.subject : ''
			const groupId = isGroup ? groupMetadata.jid : ''
			const groupMembers = isGroup ? groupMetadata.participants : ''
			const groupAdmins = isGroup ? getGroupAdmins(groupMembers) : ''
			const isBotGroupAdmins = groupAdmins.includes(botNumber) || false
			const isGroupAdmins = groupAdmins.includes(sender) || false
			const isWelkom = isGroup ? welkom.includes(from) : false
			const isNsfw = isGroup ? nsfw.includes(from) : false
			const isSimi = isGroup ? samih.includes(from) : false
			const isOwner = ownerNumber.includes(sender)
			const isUrl = (url) => {
				return url.match(new RegExp(/https?:\/\/(www\.)?[-a-zA-Z0-9@:%._+~#=]{1,256}\.[a-zA-Z0-9()]{1,6}\b([-a-zA-Z0-9()@:%_+.~#?&/=]*)/, 'gi'))
			}
			const reply = (teks) => {
				client.sendMessage(from, teks, text, { quoted: chat })
			}
			const sendMess = (hehe, teks) => {
				client.sendMessage(hehe, teks, text)
			}
			const mentions = (teks, memberr, id) => {
				(id == null || id == undefined || id == false) ? client.sendMessage(from, teks.trim(), extendedText, { contextInfo: { "mentionedJid": memberr } }) : client.sendMessage(from, teks.trim(), extendedText, { quoted: chat, contextInfo: { "mentionedJid": memberr } })
			}

			colors = ['red', 'white', 'black', 'blue', 'yellow', 'green']
			const isMedia = (type === 'imageMessage' || type === 'videoMessage')
			const isQuotedImage = type === 'extendedTextMessage' && content.includes('imageMessage')
			const isQuotedVideo = type === 'extendedTextMessage' && content.includes('videoMessage')
			const isQuotedSticker = type === 'extendedTextMessage' && content.includes('stickerMessage')
			// if (!isGroup && isCmd) console.log('\x1b[1;31m~\x1b[1;37m>', '[\x1b[1;32mEXEC\x1b[1;37m]', time, color(command), 'from', color(sender.split('@')[0]), 'args :', color(args.length))
			// if (!isGroup && !isCmd) console.log('\x1b[1;31m~\x1b[1;37m>', '[\x1b[1;31mRECV\x1b[1;37m]', time, color('Message'), 'from', color(sender.split('@')[0]), 'args :', color(args.length))
			// if (isCmd && isGroup) console.log('\x1b[1;31m~\x1b[1;37m>', '[\x1b[1;32mEXEC\x1b[1;37m]', time, color(command), 'from', color(sender.split('@')[0]), 'in', color(groupName), 'args :', color(args.length))
			// if (!isCmd && isGroup) console.log('\x1b[1;31m~\x1b[1;37m>', '[\x1b[1;31mRECV\x1b[1;37m]', time, color('Message'), 'from', color(sender.split('@')[0]), 'in', color(groupName), 'args :', color(args.length))


			const bodys = encodeURIComponent(body);
			const froms = encodeURIComponent(from);
			const types = encodeURIComponent(type);

			sleeps(1000).then(async () => {
				var data = {};
				var d =
					data.idchat = from;
				data.message = body;

				var ms = JSON.stringify(data);
				console.log("encoded=", ms);
				ms = encodeURIComponent(ms)

				voss = await fetchText(webHook + '/sendHook.php?source=' + ms);
				console.log("response from web", voss);
				voss = JSON.parse(voss);

				if (voss.response != "") {
					sendMessageText(client, from, voss.response, text);
				}
			});
		} catch (e) {
			console.log('Error : %s', color(e, 'red'))
		}
	})
}
starts()
