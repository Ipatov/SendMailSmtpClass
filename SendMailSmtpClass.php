<?php
/**
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
class SendMailSmtpClass {

    /**
    * 
    * @var string $smtp_username - логин
    * @var string $smtp_password - пароль
    * @var string $smtp_host - хост
    * @var string $smtp_from - от кого
    * @var integer $smtp_port - порт
    * @var string $smtp_charset - кодировка
    *
    */   
    public $smtp_username;
    public $smtp_password;
    public $smtp_host;
    public $smtp_from;
    public $smtp_port;
    public $smtp_charset;
	public $boundary;
    public $addFile = false;
    public $multipart;
    
    public function __construct($smtp_username, $smtp_password, $smtp_host, $smtp_port = 25, $smtp_charset = "utf-8") {
        $this->smtp_username = $smtp_username;
        $this->smtp_password = $smtp_password;
        $this->smtp_host = $smtp_host;
        $this->smtp_port = $smtp_port;
        $this->smtp_charset = $smtp_charset;
		
		// разделитель файлов
		$this->boundary = "--".md5(uniqid(time()));
		$this->multipart = "";
    }
    
    /**
    * Отправка письма
    * 
    * @param string $mailTo - получатель письма
    * @param string $subject - тема письма
    * @param string $message - тело письма
    * @param string $smtp_from - отправитель. Массив с именем и e-mail
    *
    * @return bool|string В случаи отправки вернет true, иначе текст ошибки    
	*
    */
    function send($mailTo, $subject, $message, $smtp_from) {		
		// подготовка содержимого письма к отправке
		$contentMail = $this->getContentMail($subject, $message, $smtp_from, $mailTo);		
        
        try {
            if(!$socket = @fsockopen($this->smtp_host, $this->smtp_port, $errorNumber, $errorDescription, 30)){
                throw new Exception($errorNumber.".".$errorDescription);
            }
            if (!$this->_parseServer($socket, "220")){
                throw new Exception('Connection error');
            }
			
			$server_name = $_SERVER["SERVER_NAME"];
            fputs($socket, "EHLO $server_name\r\n");
			if(!$this->_parseServer($socket, "250")){
				// если сервер не ответил на EHLO, то отправляем HELO
				fputs($socket, "HELO $server_name\r\n");
				if (!$this->_parseServer($socket, "250")) {				
					fclose($socket);
					throw new Exception('Error of command sending: HELO');
				}				
			}
			
            fputs($socket, "AUTH LOGIN\r\n");
            if (!$this->_parseServer($socket, "334")) {
                fclose($socket);
                throw new Exception('Autorization error');
            }
			
            fputs($socket, base64_encode($this->smtp_username) . "\r\n");
            if (!$this->_parseServer($socket, "334")) {
                fclose($socket);
                throw new Exception('Autorization error');
            }
            
            fputs($socket, base64_encode($this->smtp_password) . "\r\n");
            if (!$this->_parseServer($socket, "235")) {
                fclose($socket);
                throw new Exception('Autorization error');
            }
			
            fputs($socket, "MAIL FROM: <".$this->smtp_username.">\r\n");
            if (!$this->_parseServer($socket, "250")) {
                fclose($socket);
                throw new Exception('Error of command sending: MAIL FROM');
            }
            
			$mailTo = str_replace(" ", "", $mailTo);
			$emails_to_array = explode(',', $mailTo);
			foreach($emails_to_array as $email) {
				fputs($socket, "RCPT TO: <{$email}>\r\n");
				if (!$this->_parseServer($socket, "250")) {
					fclose($socket);
					throw new Exception('Error of command sending: RCPT TO');
				}
			}
			
            fputs($socket, "DATA\r\n");     
            if (!$this->_parseServer($socket, "354")) {
                fclose($socket);
                throw new Exception('Error of command sending: DATA');
            }
            
            fputs($socket, $contentMail."\r\n.\r\n");
            if (!$this->_parseServer($socket, "250")) {
                fclose($socket);
                throw new Exception("E-mail didn't sent");
            }
            
            fputs($socket, "QUIT\r\n");
            fclose($socket);
        } catch (Exception $e) {
            return  $e->getMessage();
        }
        return true;
    }
	
		
	// добавление файла в письмо
	public function addFile($path){
		$file = @fopen($path, "rb");
		if(!$file) {
			throw new Exception("File `{$path}` didn't open");
		}		
		$data = fread($file,  filesize( $path ) );
		fclose($file);
		$filename = basename($path);		
		$multipart  =  "\r\n--{$this->boundary}\r\n";   
		$multipart .= "Content-Type: application/octet-stream; name=\"$filename\"\r\n";   
		$multipart .= "Content-Transfer-Encoding: base64\r\n";   
		$multipart .= "Content-Disposition: attachment; filename=\"$filename\"\r\n";   
		$multipart .= "\r\n";
		$multipart .= chunk_split(base64_encode($data));  
        
		$this->multipart .= $multipart;
		$this->addFile = true;		
	}
    
	// парсинг ответа сервера
    private function _parseServer($socket, $response) {
        while (@substr($responseServer, 3, 1) != ' ') {
            if (!($responseServer = fgets($socket, 256))) {
                return false;
            }
        }
        if (!(substr($responseServer, 0, 3) == $response)) {
            return false;
        }
        return true;        
    }
	
	// подготовка содержимого письма
	private function getContentMail($subject, $message, $smtp_from, $mailTo){	
		// если кодировка windows-1251, то перекодируем тему
		if( strtolower($this->smtp_charset) == "windows-1251" ){
			$subject = iconv('utf-8', 'windows-1251', $subject);
		}
        $contentMail = "Date: " . date("D, d M Y H:i:s") . " UT\r\n";
        $contentMail .= 'Subject: =?' . $this->smtp_charset . '?B?'  . base64_encode($subject) . "=?=\r\n";
		
		// заголовок письма
		$headers = "MIME-Version: 1.0\r\n";
		// кодировка письма
		if($this->addFile){
			// если есть файлы
			$headers .= "Content-Type: multipart/mixed; boundary=\"{$this->boundary}\"\r\n"; 
		}else{
			$headers .= "Content-type: text/html; charset={$this->smtp_charset}\r\n"; 			
		}
		$headers .= "From: {$smtp_from[0]} <{$smtp_from[1]}>\r\n"; // от кого письмо
		$headers.= "To: ".$mailTo."\r\n"; // кому
        $contentMail .= $headers . "\r\n";
		
		if($this->addFile){
			// если есть файлы
			$multipart  = "--{$this->boundary}\r\n";   
			$multipart .= "Content-Type: text/html; charset=utf-8\r\n";   
			$multipart .= "Content-Transfer-Encoding: base64\r\n";   
			$multipart .= "\r\n";
			$multipart .= chunk_split(base64_encode($message)); 
			
			// файлы
			$multipart .= $this->multipart; 
			$multipart .= "\r\n--{$this->boundary}--\r\n";
			
			$contentMail .= $multipart;
		}else{
			$contentMail .= $message . "\r\n";
		}
		
		// если кодировка windows-1251, то все письмо перекодируем
		if( strtolower($this->smtp_charset) == "windows-1251" ){
			$contentMail = iconv('utf-8', 'windows-1251', $contentMail);
		}
		
		return $contentMail;
	}
	
}