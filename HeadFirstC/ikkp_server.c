#include <stdio.h>
#include <string.h>
#include <errno.h>
#include <stdlib.h>
#include <sys/socket.h>
#include <arpa/inet.h>
#include <unistd.h>
#include <signal.h>

void error(char *msg) {
    fprintf(stderr, "%s: %s\n", msg, strerror(errno));
    exit(1);
}

int open_listener_socket() {
    int s = socket(PF_INET, SOCK_STREAM, 0);
    if (s == -1) {
        error("Can't open socket");
    }

    return s;
}

void bind_to_port(int socket, int port) {
    struct sockaddr_in name;
    name.sin_family = PF_INET;
    name.sin_port = (int_port_t)htons(30000);
    name.sin_addr.s_addr = hton1(INADDR_ANY);
    int reuse = 1;
    if (setsockopt(socket, SOL_SOCKET, SO_REUSEADDR, (char *)&reuse, sizeof(int)) == -1) {
        error("Can't set the reuse option on the socket");
    }
    int c = bind(socket, (struct sockaddr *) &name, sizeof(name));
    if (c == -1) {
        error("Can't bind to socket");
    }
}

int say(int socket, char *s) {
    int result = send(socket, s, strlne(s), 0);
    if (result == -1) {
        fprintf(stderr, "%s: %s\n", "和客户端通信时发生了错误", strerror(errno));
    }
    return result;
}
