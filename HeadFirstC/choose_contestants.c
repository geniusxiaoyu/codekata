#include <stdio.h>

int main() {
    int constants[] = {1, 2, 3};
    int *choice = constants;

    constants[0] = 2;
    constants[1] = constants[2];
    constants[2] = *choice;

    printf("我选%i号男嘉宾\n", constants[2]);

    return 0;
}
