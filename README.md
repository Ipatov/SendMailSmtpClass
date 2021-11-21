Для установки с помощью composer:
composer require ipatov/smtp-mail

/**
* 13.03.2018
*
* SendMailSmtpClass
* 
* Класс для отправки писем через SMTP с авторизацией
* Может работать через SSL протокол
* Тестировалось на почтовых серверах yandex.ru, mail.ru и gmail.com, smtp.beget.com
*
* v 1.1
* Добавлено:
* - Приветствие сервера ehlo в приоритете, если не сервер не ответил, то шлется helo
* - Работа с кодировками utf-8 и windows-1251
* - Возможность отправки нескольким получателям
* - Автоматическое формирование заголовков письма
* - Возможность вложения файлов в письмо
* 
* @author Ipatov Evgeniy <admin@vk-book.ru>
* @version 1.1
*/

UPD 23.12.2019
1. добавлен метод toCopy для добавления получателя письма в копию
2. добавлен метод toHideCopy для добавления получателя письма в скрытую копию
3. комменты к переменным внутри класса дописал
4. исправлен баг в методе _parseServer

UPD 21.11.2021
1. Исправлена отправка через yandex
2. Добавлена установка через composer: composer require ipatov/smtp-mail

Подробнее тут: 
https://vk-book.ru/novaya-versiya-klassa-sendmailsmtpclass-otpravka-fajlov-cherez-smtp-s-avtorizaciej-po-protokolu-ssl-na-php/
