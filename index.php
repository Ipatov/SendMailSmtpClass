<?php
	// пример использования SendMailSmtpClass.php

	require_once "SendMailSmtpClass.php"; // подключаем класс
	  
	// примеры подключения	
	$mailSMTP = new SendMailSmtpClass('zhenikipatov@yandex.ru', '***', 'ssl://smtp.yandex.ru', 465, "UTF-8");
	// $mailSMTP = new SendMailSmtpClass('zhenikipatov@yandex.ru', '***', 'ssl://smtp.yandex.ru', 465, "windows-1251");
	// $mailSMTP = new SendMailSmtpClass('monitor.test@mail.ru', '***', 'ssl://smtp.mail.ru', 465, "UTF-8");
	// $mailSMTP = new SendMailSmtpClass('red@mega-dev.ru', '***', 'ssl://smtp.beget.com', 465, "UTF-8");
	// $mailSMTP = new SendMailSmtpClass('red@mega-dev.ru', '***', 'smtp.beget.com', 2525, "windows-1251");
	// $mailSMTP = new SendMailSmtpClass('red@mega-dev.ru', '***', 'ssl://smtp.beget.com', 465, "utf-8");
	// $mailSMTP = new SendMailSmtpClass('red@mega-dev.ru', '***', 'smtp.beget.com', 2525, "utf-8");
	// $mailSMTP = new SendMailSmtpClass('логин', 'пароль', 'хост', 'порт', 'кодировка письма');
		
	// от кого
	$from = array(
		"Евгений", // Имя отправителя
		"test@vk-book.ru" // почта отправителя
	);
	// кому отправка. Можно указывать несколько получателей через запятую
	$to = 'admin@vk-book.ru, ipatovsoft@gmail.com';
	
	// добавляем файлы
	$mailSMTP->addFile("test.jpg");
	$mailSMTP->addFile("test2.jpg");
	$mailSMTP->addFile("test3.txt");
	
	// добавить получателя письма в копию
	$mailSMTP->toCopy("test-copy@yandex.ru"); 
	$mailSMTP->toCopy("test-copy@vk-book.ru");
	
	// добавить получателя письма в скрытую копию
	$mailSMTP->toHideCopy("zhenikipatov@yandex.ru");
	
	// отправляем письмо
	$result =  $mailSMTP->send($to, 'Тема письма с копиями ', 'Текст письма', $from); 
	// $result =  $mailSMTP->send('Кому письмо', 'Тема письма', 'Текст письма', 'Отправитель письма');
	
	if($result === true){
		echo "Done";
	}else{
		echo "Error: " . $result;
	}
	