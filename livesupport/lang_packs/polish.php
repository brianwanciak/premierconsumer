<?php
/***********************************************************************/
// Polish language pack
// Revised by: info@osicodesinc.com
/***********************************************************************/
/* Please submit corrections to info@osicodesinc.com - Thank you! */

// UWAGA: Pamietaj, ze po zainstalowaniu PHP LIVE! i po stworzeniu dzialów, nalezy zmienic
// tlumaczenie na jezyk polski w ustawieniach: "Chat Greeting", "Offline Message" i "Transcript Email" 

$LANG = Array() ;
// Nie próbuj zmieniac charset, chyba ze znaki nie sa wyswietlane prawidlowo
// Jezeli znaki nie sa wyswietlane prawidlowo, zmien w wyrazach odpowiednie znaki na polskie znaki diakrytyczne tz. na: a, c, e, l, n, ó, s, z, z
$LANG["CHARSET"] = "UTF-8" ;

/* Okno uzytkownika chatu */
$LANG["CHAT_WELCOME"] = "Witamy na naszej Czat na żywo" ;
$LANG["CHAT_WELCOME_SUBTEXT"] = "Aby lepiej pomóc, prosimy o podanie następujących informacji." ;
$LANG["CHAT_SELECT_DEPT"] = "--- wybierz dział ---" ;
$LANG["CHAT_BTN_START_CHAT"] = "Rozpocznij czat" ;
$LANG["CHAT_BTN_EMAIL"] = "Wyślij e-mail" ;
$LANG["CHAT_BTN_EMAIL_TRANS"] = "Wyślij odpis" ;
$LANG["CHAT_PRINT"] = "Drukuj Zapis" ;
$LANG["CHAT_CHAT_WITH"] = "Czat sesja z" ;
$LANG["CHAT_SURVEY_THANK"] = "Twoja ocena została złożona. Dziękuję." ;
$LANG["CHAT_CLOSE"] = "Zamknij okno" ;
$LANG["CHAT_SOUND"] = "Przełączanie dźwięku" ;
$LANG["CHAT_TRANSFER"] = "Przesyłanie czat" ;
$LANG["CHAT_TRANSFER_TIMEOUT"] = "Przeniesienie rozmowy nie są dostępne w tym czasie. Podłączenie do poprzedniego operatora..." ;


/* Obszar wiadomosci */
$LANG["MSG_LEAVE_MESSAGE"] = "Proszę zostawić wiadomość." ;
$LANG["MSG_EMAIL_FOOTER"] = "Wiadomość wysłana przez Live Chat zostaw wiadomość." ;
$LANG["MSG_PROCESSING"] = "Poprzednia wiadomość jest nadal przetwarzane. Spróbuj ponownie wkrótce." ;


/* Tekst wewnetrzny */
$LANG["TRANSCRIPT_SUBJECT"] = "Zapis rozmowy z" ;


/*Chat powiadomienia */
$LANG["CHAT_NOTIFY_JOINED"] = "przyłączył się do rozmowy." ;
$LANG["CHAT_NOTIFY_RATE"] = "Jak oceniasz tę sesję wsparcia?" ;
$LANG["CHAT_NOTIFY_DISCONNECT"] = "Strona w lewo lub odłączony. Czat sesja zakończyła." ;
$LANG["CHAT_NOTIFY_VDISCONNECT"] = "Odłączony przez odwiedzającego. Czat sesja zakończyła." ;
$LANG["CHAT_NOTIFY_ODISCONNECT"] = "Odłączony przez operatora. Czat sesja zakończyła." ;
$LANG["CHAT_NOTIFY_LOOKING_FOR_OP"] = "Środek będzie z Państwem wkrótce %%visitor%%. Dziękujemy za cierpliwość." ;
$LANG["CHAT_NOTIFY_OP_NOT_FOUND"] = "Agenci nie są dostępne w tym czasie. Proszę zostawić wiadomość." ;
$LANG["CHAT_NOTIFY_IDLE_TITLE"] = "Czat jest bezczynny. Proszę wysłać odpowiedź." ;
$LANG["CHAT_NOTIFY_IDLE_AUTO_DISCONNECT"] = "Automatyczne odłączanie czat" ;


/* Javascript alerts */
$LANG["CHAT_JS_BLANK_DEPT"] = "Proszę wybrać dział." ;
$LANG["CHAT_JS_BLANK_NAME"] = "Proszę podać swoje imię i nazwisko." ;
$LANG["CHAT_JS_BLANK_EMAIL"] = "Proszę podać swój adres e-mail." ;
$LANG["CHAT_JS_INVALID_EMAIL"] = "Format E-mail jest nieprawidłowy. (przykład: kowalski@com.pl)" ;
$LANG["CHAT_JS_BLANK_SUBJECT"] = "Proszę podać temat." ;
$LANG["CHAT_JS_BLANK_QUESTION"] = "Proszę podać pytanie." ;
$LANG["CHAT_JS_LEAVE_MSG"] = "Czat na żywo Język: Zostaw wiadomość" ;
$LANG["CHAT_JS_EMAIL_SENT"] = "E-mail wysłany" ;
$LANG["CHAT_JS_CHAT_EXIT"] = "Dziękujemy za rozmowy z nami." ;
$LANG["CHAT_JS_CUSTOM_BLANK"] = "Proszę podać swoje" ;


/* Skróty */
$LANG["TXT_DEPARTMENT"] = "dział" ;
$LANG["TXT_ONLINE"] = "Online" ;
$LANG["TXT_OFFLINE"] = "Offline" ;
$LANG["TXT_NAME"] = "Imię" ;
$LANG["TXT_EMAIL"] = "Email" ;
$LANG["TXT_QUESTION"] = "Pytanie" ;
$LANG["TXT_CONNECT"] = "Połączyć" ;
$LANG["TXT_CONNECTING"] = "Podłączanie..." ;
$LANG["TXT_SUBMIT"] = "Zgłaszać" ;
$LANG["TXT_DISCONNECT"] = "odłączyć" ;
$LANG["TXT_SUBJECT"] = "Przedmiot" ;
$LANG["TXT_MESSAGE"] = "Wiadomość" ;
$LANG["TXT_LIVECHAT"] = "Czat na żywo" ;
$LANG["TXT_OPTIONAL"] = "fakultatywny" ;
$LANG["TXT_TYPING"] = "jest wpisanie..." ;
$LANG["TXT_SECONDS"] = "sekunda" ;


/* as of v.4.5.9, all new lang vars will be included here in sequential order */
$LANG["CHAT_COMMENT_THANK"] = "Komentarz odbierane. Dziękuję Ci." ;
$LANG["CHAT_JS_BLANK_COMMENT"] = "Proszę podać komentarz." ;
?>