<?php

declare(strict_types = 1);

namespace WMDE\Fundraising\Frontend;

use Swift_Message;
use Swift_Transport;

/**
 * @licence GNU GPL v2+
 * @author Kai Nissen < kai.nissen@wikimedia.de >
 */
class Messenger {

	private $mailTransport;
	private $operatorAddress;
	private $operatorName;

	public function __construct( Swift_Transport $mailTransport,
								 MailAddress $operatorAddress,
								 string $operatorName = '' ) {
		$this->mailTransport = $mailTransport;
		$this->operatorAddress = $operatorAddress;
		$this->operatorName = $operatorName;
	}

	public function sendMessageToUser( Message $messageContent, MailAddress $recipient ) {
		$this->sendMessage( $this->createMessage( $messageContent, $recipient ) );
	}

	public function sendMessageToOperator( Message $messageContent, MailAddress $replyTo = null ) {
		$this->sendMessage( $this->createMessage( $messageContent, $this->operatorAddress, $replyTo ) );
	}

	private function createMessage( Message $messageContent, MailAddress $recipient,
									MailAddress $replyTo = null ): Swift_Message {
		$message = Swift_Message::newInstance( $messageContent->getSubject(), $messageContent->getMessageBody() );
		$message->setFrom( $this->operatorAddress->getFullAddress(), $this->operatorName );
		$message->setTo( $recipient->getFullAddress() );
		if ( $replyTo ) {
			$message->setReplyTo( $replyTo->getFullAddress() );
		}

		return $message;
	}

	private function sendMessage( Swift_Message $message ) {
		$deliveryCount = $this->mailTransport->send( $message );
		if ( $deliveryCount === 0 ) {
			throw new \RuntimeException( 'Message delivery failed' );
		}
	}

}
