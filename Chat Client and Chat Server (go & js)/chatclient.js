var net = require('net');
var readlineSync = require('readline-sync');
var username;
var password;
function loginsync(){
	// Wait for user's response.
	//keyboard.pause()
	username = readlineSync.question('Username: ');
	if (!inputValidated(username)){
		console.log("Username must have at least 5 characters. Please try again!");
		loginsync();
		return;
	}
	// Handle the secret text (e.g. password).
	password = readlineSync.question('Password: ', {
		hideEchoBack: true // The typed text on screen is hidden by `*` (default).
	});
	if (!inputValidated(password)) {
		console.log("Password must have at least 5 characters. Please try again!");
		loginsync();
		return;
	}
	//expecting format of {"username":"..","password":".."}
	var p1 = "{\"username\":\"" + username + "\",";
	var p2 = "\"password\":\"" + password + "\"}";
	var login = p1 + p2;
	client.write(login);
	//keyboard.prompt()
}

function chatroom(){
	// console.log("You have logged in successfully with username " + username);
	// console.log("Welcome to the Chat System. Type anything to send to public chat.\n");
	
	// console.log("Type: '[To:Receiver] Message' to send to a specific user.");
	// console.log("Type: .userList to request latest online users.");
	// console.log("Type: .exit to logout and close connection.");

	// while (true){
	// 	chatMsg = readlineSync.question('> ');
	// 	client.write(chatMsg);
	// }

	// chatMsg = readlineSync.question('> ');
	// client.write(chatMsg);
}

if(process.argv.length != 4){
	console.log("Usage: node %s <host> <port>", process.argv[1]);
	process.exit(1);	
}

var host=process.argv[2];
var port=process.argv[3];

if(host.length >253 || port.length >5 ){
	console.log("Invalid host or port. Try again!\nUsage: node %s <port>", process.argv[1]);
	process.exit(1);	
}

var client = new net.Socket();
console.log("Simple telnet.js developed by Phu Phung, SecAD, Revised by John Conroy");
console.log("Connecting to: %s:%s", host, port);

client.connect(port,host, connected);

function connected(){
	console.log("Connected to: %s:%s", client.remoteAddress, client.remotePort);
	console.log("You need to login before sending/receiving messages.\n");
	keyboard.pause()
	loginsync();
	keyboard.prompt()
}

function inputValidated(data) {
	if (data.length > 4) {
		return true;
	}
	else{
		return false;
	}
}

client.on("data", data => {
	if (data.includes('Invalid username or password')){
		console.log("Received data:" + data);
		console.log("Authentication failed. Please try again");
		keyboard.pause()
		loginsync();
		keyboard.prompt()
	}
	else if (data.includes('[authenticated]')){
		console.log("Received data:" + data);

		console.log("You have logged in successfully with username " + username);
		console.log("Welcome to the Chat System. Type anything to send to public chat.\n");
		
		console.log("Type: '[To:Receiver] Message' to send to a specific user.");
		console.log("Type: .userList to request latest online users.");
		console.log("Type: .exit to logout and close connection.");
		//chatroom(); //************************************************************
	}
	else if (data.includes('.exit')){
		console.log("Connection has been disconnected");
		process.exit(3);
	}
	else {
		console.log("Received data:" + data);
	}
	

});

client.on("error", function(err){
	console.log("Error");
	process.exit(2);
});

client.on("close", function(data){
	console.log("Connection has been disconnected");
	process.exit(3);
});

const keyboard = require('readline').createInterface({
	input: process.stdin,
	//output: process.stdout
});

keyboard.on('line', (input) => {
	// console.log(`You typed: ${input}`);
	// if (input === ".exit"){
	// 	client.destroy();
	// 	console.log("disconnected!");
	// 	process.exit();
	// }else
	// 	client.write(input)
	client.write(input)
});


