/* Simple ChatServer in GoLang by Phu Phung, customized by John Conroy for SecAD*/
package main

import (
	"fmt"
	"net"
	"os"
	"encoding/json"
	"strings"
)

const BUFFERSIZE int = 1024
var allClient_conns = make(map[net.Conn]string) // global
var AuthenticatedClient_conns = make(map[net.Conn]string)
var AuthenticatedUser_list = make(map[net.Conn]string)
var newclient = make(chan net.Conn)
var lostclient = make(chan net.Conn)
// var HARDCODE_USER string = "admin"
// var HARDCODE_PASS string = "password"

func main() {
	if len(os.Args) != 2 {
		fmt.Printf("Usage: %s <port>\n", os.Args[0])
		os.Exit(0)
	}
	port := os.Args[1]
	if len(port) > 5 {
		fmt.Println("Invalid port value. Try again!")
		os.Exit(1)
	}
	server, err := net.Listen("tcp", ":"+port)
	if err != nil {
		fmt.Printf("Cannot listen on port '" + port + "'!\n")
		os.Exit(2)
	}
	fmt.Println("ChatServer in GoLang developed by Phu Phung, SecAD, revised by John Conroy")
	fmt.Printf("ChatServer is listening on port '%s' ...\n", port)
	
	go func (){	
		for {
			client_conn, _ := server.Accept()
			fmt.Println("A new client is connected from " + client_conn.RemoteAddr().String() + ". Waiting for login!")
			go login(client_conn)
		}		
	}()
	for {
		select{
			case client_conn := <- newclient:
				go authenticating(client_conn)
			case client_conn := <- lostclient: //Step 3
				delete(AuthenticatedClient_conns, client_conn)
				delete(AuthenticatedUser_list, client_conn)
				byemessage := fmt.Sprintf("A client '%s' DISCONNECTED!\n# of connected clients: %d\n",
					client_conn.RemoteAddr().String(), len(AuthenticatedClient_conns))
				fmt.Println(byemessage)
				go sendtoAll([]byte (byemessage))
		}
	}
}

func authenticating(client_conn net.Conn) {
	AuthenticatedClient_conns[client_conn] = client_conn.RemoteAddr().String()
	var new_user_name string = string(AuthenticatedUser_list[client_conn])
	sendto(client_conn,[]byte("[authenticated]\n"))
	welcomemessage := fmt.Sprintf("New user '%s' logged in to Chat System from %s\n%s (from %d connections)\n",
					new_user_name, client_conn.RemoteAddr().String(), getUserList(), len(AuthenticatedClient_conns))
	fmt.Println("DEBUG>Sent data: " + welcomemessage + "to all logged in clients")
	sendtoAll([]byte (welcomemessage))
	go client_goroutine(client_conn)
}

func login(client_conn net.Conn) {
	var buffer [BUFFERSIZE]byte
	go func(){
		//THIS FOR LOOP CAUSED AN INFINITE LOGIN LOOP
		//for {
			byte_received, read_err := client_conn.Read(buffer[0:])
			if read_err != nil {
				fmt.Println("Error in receiving...")
				lostclient <- client_conn
				return
			}

			//CALLS CHECKLOGIN
			logindata := buffer[0:byte_received]
			fmt.Printf("DEBUG>Got data: %s. Expecting login data\n", logindata)
			authenticated, username, loginmessage := checklogin(logindata)			
			if authenticated {
			//if len(clientdata) >= 5 && strings.Compare(string(clientdata[0:5]),"login") == 0 {
				fmt.Println("DEBUG>login data. User '" + username + "' is successfully logged in!")
				AuthenticatedUser_list[client_conn] = string(username) //LOG USERLIST
				newclient <- client_conn

			}else{
				//fmt.Println("DEBUG>non-login data. Error Message: " + loginmessage)
				sendto(client_conn,[]byte(loginmessage))
				login(client_conn)
			}
		//}
	}()
}

func checklogin(data []byte) (bool, string, string) {
	//expecting format of {"username":"..","password":".."}
	type Account struct{
		Username string
		Password string
	}
	var account Account
	err := json.Unmarshal(data, &account)
	if err!=nil || account.Username == "" || account.Password == "" {
		fmt.Printf("JSON parsing error: %s\n", err)
		return false, "", `[BAD LOGIN] Expected: {"Username":"..","Password":".."}`
	}
	fmt.Printf("DEBUG>Got: account=%s\n", account)
	fmt.Printf("DEBUG>Got: username=%s, password=%s\n", account.Username,account.Password)

	if checkaccount(account.Username, account.Password) {
		fmt.Println("DEBUG>Username & password are found!")
		return true, account.Username, "logged"
	}

	return false, "", "Invalid username or password\n"
}

func checkaccount(user string, pass string) (bool){
	var HARDCODE_USER1 string = "user1"
	var HARDCODE_PASS1 string = "pass1"
	var HARDCODE_USER2 string = "user2"
	var HARDCODE_PASS2 string = "pass2"
	var HARDCODE_USER3 string = "user3"
	var HARDCODE_PASS3 string = "pass3"

	if user == HARDCODE_USER1 && pass == HARDCODE_PASS1 {
		return true
	}else if user == HARDCODE_USER2 && pass == HARDCODE_PASS2{
		return true
	}else if user == HARDCODE_USER3 && pass == HARDCODE_PASS3{
		return true
	}else{
		fmt.Println("DEBUG>Invalid username or password.")
		return false
	}
}

func sendto(client_conn net.Conn, data []byte) {
	_, write_err := client_conn.Write(data)
	if write_err != nil {
			fmt.Println("Error in sending...to " + client_conn.RemoteAddr().String())
			return
	}
}

func sendtoAll(data []byte){
	for client_conn, _:= range AuthenticatedClient_conns{
		_, write_err := client_conn.Write(data)
		if write_err != nil {
			fmt.Println("Error in sending...")
			continue
		}
	}
	//fmt.Printf("Received Data: %s Sent to all connected clients!\n", data)
}

func getUserList() (string) {
	var USER_LIST string = "Online Users: "
	for client_conn, _:= range AuthenticatedUser_list{
		var temp_user string = string(AuthenticatedUser_list[client_conn]) + ", "
		USER_LIST = USER_LIST + temp_user
	}
	fmt.Println("DEBUG>getUserList()->userlist=[" + USER_LIST +"]")
	return USER_LIST
}

func getClientConn(name string) (net.Conn) {
	var final_client_conn net.Conn //= newclient //newclient here is just a placeholder
	for client_conn, _:= range AuthenticatedUser_list{
		var temp_user string = string(AuthenticatedUser_list[client_conn])
		if (temp_user == name){
			final_client_conn = client_conn
		}
	}
	return final_client_conn
} 

func client_goroutine(client_conn net.Conn){
	var buffer [BUFFERSIZE]byte
	go func(){
		for {
			byte_received, read_err := client_conn.Read(buffer[0:])
			if read_err != nil {
				fmt.Println("Error in receiving...")
				lostclient <- client_conn
				return
			}
			client_data := buffer[0:byte_received]
			fmt.Printf("Received data: {%s} from %s\n", client_data, client_conn.RemoteAddr().String())
			

			//Send user_list to client.
			if (string(client_data) == ".userList"){ //USER LIST
				//
				//
				//fmt.Println("Send USER LIST")
				var client_user_list string = getUserList()
				client_conn.Write([]byte(client_user_list))
				//
				//
			} else if (string(client_data) == ".exit"){ //LOGOUT
				//
				//
				//fmt.Println("LOGOUT")
				//*Remove client_conn from lists?
				var exit_string string = ".exit"
				client_conn.Write([]byte(exit_string))
				//
				//
			} else if (string(client_data[0:4]) == "[To:"){ //PRIVATE MESSAGE
				//
				//
				//'[To:Receiver] Message'
				var x int = int(strings.Index(string(client_data), "]"))
				var y int = int(len(string(client_data)))
				var private_user string = string(client_data[4:x])
				var private_message string = string(client_data[x+2:y])
				var private_user_conn net.Conn = getClientConn(private_user)
				var final_private_msg string = "Private message from '" + string(AuthenticatedUser_list[client_conn]) + "': " + private_message
				
				fmt.Println("DEBUG>Sent data: Private message from '" + string(AuthenticatedUser_list[client_conn]) + "' to '" + private_user + "': " + string(private_message))
				sendto(private_user_conn, []byte(final_private_msg))
				//
				//
			} else{ //PUBLIC MESSAGE
				//
				//
				//fmt.Println("SEND PUBLIC CHAT")
				var final_public_msg string = "Public message from '" + string(AuthenticatedUser_list[client_conn]) + "': " + string(client_data)
				fmt.Println("DEBUG>Sent data: Public message from '" + string(AuthenticatedUser_list[client_conn]) + "': " + string(client_data) + " to all logged in clients")
				sendtoAll([]byte(final_public_msg))
				//
				//
			}
		}
	}()
}
