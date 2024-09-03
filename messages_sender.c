#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <windows.h>
#include <winhttp.h>
#include <time.h>

#pragma comment(lib, "winhttp.lib")

#define BUFFER_SIZE 1024

void send_message(const char *message) {
    HINTERNET hSession, hConnect, hRequest;
    BOOL bResults;
    char requestData[256];
    LPCWSTR host = L"server-of-bowen.pxl.bjth.xyz";
    LPCWSTR resource = L"/api/v1/massages.php";

    hSession = WinHttpOpen(L"A WinHTTP Example Program/1.0",
                           WINHTTP_ACCESS_TYPE_DEFAULT_PROXY,
                           WINHTTP_NO_PROXY_NAME,
                           WINHTTP_NO_PROXY_BYPASS,
                           0);
    if (!hSession) return;

    hConnect = WinHttpConnect(hSession, host, INTERNET_DEFAULT_HTTP_PORT, 0);
    if (!hConnect) {
        WinHttpCloseHandle(hSession);
        return;
    }

    hRequest = WinHttpOpenRequest(hConnect, L"POST", resource, NULL, WINHTTP_NO_REFERER, WINHTTP_DEFAULT_ACCEPT_TYPES, 0);
    if (!hRequest) {
        WinHttpCloseHandle(hConnect);
        WinHttpCloseHandle(hSession);
        return;
    }

    snprintf(requestData, sizeof(requestData), "{\"message\": \"%s\"}", message);

    const wchar_t *headers = L"Content-Type: application/json";
    WinHttpAddRequestHeaders(hRequest, headers, -1L, WINHTTP_ADDREQ_FLAG_ADD | WINHTTP_ADDREQ_FLAG_REPLACE);

    bResults = WinHttpSendRequest(hRequest, WINHTTP_NO_ADDITIONAL_HEADERS, 0, requestData, (DWORD)strlen(requestData), (DWORD)strlen(requestData), 0);
    if (!bResults) {
        WinHttpCloseHandle(hRequest);
        WinHttpCloseHandle(hConnect);
        WinHttpCloseHandle(hSession);
        return;
    }

    bResults = WinHttpReceiveResponse(hRequest, NULL);
    if (bResults) {
        char buffer[BUFFER_SIZE];
        DWORD dwSize = 0;
        DWORD dwDownloaded = 0;

        memset(buffer, 0, sizeof(buffer));

        while (WinHttpQueryDataAvailable(hRequest, &dwSize) && dwSize > 0) {
            if (dwSize > sizeof(buffer) - 1) {
                dwSize = sizeof(buffer) - 1;
            }

            if (WinHttpReadData(hRequest, buffer, dwSize, &dwDownloaded)) {
                buffer[dwDownloaded] = '\0'; 
                printf("Response: %s\n", buffer);
            }
        }
    }

    WinHttpCloseHandle(hRequest);
    WinHttpCloseHandle(hConnect);
    WinHttpCloseHandle(hSession);
}

int main() {
    const char *messages[] = {
        "Hello, world!",
        "Random message 1",
        "Another random message",
        "Here's a new message",
        "Message of the hour",
        "Keep it going!",
        "Here's a fun fact",
        "Did you know?",
        "Have a great day!",
        "Stay positive!"
    };

    int num_messages = sizeof(messages) / sizeof(messages[0]);
    srand((unsigned int)time(NULL));

    while (1) {
        int index = rand() % num_messages;
        const char *message = messages[index];

        send_message(message);

        Sleep(5000);
    }

    return 0;
}
