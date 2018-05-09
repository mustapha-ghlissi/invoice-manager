<?php
namespace Email\Sender;

class EmailSender
{
	public static function sendEmail($to, $subject, $body, $pdfContent, $fileName)
	{
    // You have to configure the smtp, port, encryption, username and password of your web mailer
    $transport = (new \Swift_SmtpTransport('enter-your-smtp', 465, 'ssl-ou-tls'))
    ->setUsername('you-email-here')
    ->setPassword('your-password-here')
    ;

		// Create the attachment with your data
		$attachment = new \Swift_Attachment($pdfContent, $fileName, 'application/pdf');

    $message = (new \Swift_Message())
      ->setSubject($subject)
      ->setFrom(['you-email-here' => 'you-name-here']) // Put here your email sender and name
      ->setTo($to)
      ->setBody($body)
      ->attach($attachment);

    // Create the Mailer using your created Transport
    $mailer = new \Swift_Mailer($transport);

    // Send the message
    $mailer->send($message);
	}
}
