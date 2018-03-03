<?php
	// пример использования SendMailSmtpClass.php

	require_once "SendMailSmtpClass.php"; // подключаем класс
	  
	// $mailSMTP = new SendMailSmtpClass('zhenikipatov@yandex.ru', '***', 'ssl://smtp.yandex.ru', 465, "UTF-8");
	// $mailSMTP = new SendMailSmtpClass('zhenikipatov@yandex.ru', '***', 'ssl://smtp.yandex.ru', 465, "windows-1251");
	// $mailSMTP = new SendMailSmtpClass('monitor.test@mail.ru', '***', 'ssl://smtp.mail.ru', 465, "UTF-8");
	// $mailSMTP = new SendMailSmtpClass('red@mega-dev.ru', '***', 'ssl://smtp.beget.com', 465, "UTF-8");
	// $mailSMTP = new SendMailSmtpClass('red@mega-dev.ru', '***', 'smtp.beget.com', 2525, "windows-1251");
	// $mailSMTP = new SendMailSmtpClass('red@mega-dev.ru', '***', 'ssl://smtp.beget.com', 465, "utf-8");
	$mailSMTP = new SendMailSmtpClass('red@mega-dev.ru', '***', 'smtp.beget.com', 2525, "utf-8");
	// $mailSMTP = new SendMailSmtpClass('логин', 'пароль', 'хост', 'порт', 'кодировка письма');
	
	
	// от кого
	$from = array(
		"Евгений", // Имя отправителя
		"test@vk-book.ru" // почта отправителя
	);
	// кому отправка. Можно указывать несколько получателей через запятую
	$to = 'zhenikipatov@yandex.ru, ipatovsoft@gmail.com';
	
	// добавляем файлы
	$mailSMTP->addFile("test.jpg");
	$mailSMTP->addFile("test2.jpg");
	$mailSMTP->addFile("test3.txt");
	
	// отправляем письмо
	$result =  $mailSMTP->send($to, 'Тема письма', 'Текст письма', $from); 
	// $result =  $mailSMTP->send('Кому письмо', 'Тема письма', 'Текст письма', 'Отправитель письма');
	
	if($result === true){
		echo "Done";
	}else{
		echo "Error: " . $result;
	}
	