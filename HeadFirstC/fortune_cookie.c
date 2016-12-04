#include <stdio.h>

void fortune_cookie(char msg[]) {
    printf("Message reads:%s\n", msg);
    printf("Message occupies %i bytes\n", sizeof(msg));
}
int main() {

    char quote[] = "Cookies make me fat";
    fortune_cookie(quote);

    return 0;
}
