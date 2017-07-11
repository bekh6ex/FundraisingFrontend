<?php

declare( strict_types = 1 );

namespace WMDE\Fundraising\Frontend\Tests\EdgeToEdge\Routes;

use WMDE\Fundraising\Frontend\Tests\EdgeToEdge\WebRouteTestCase;

/**
 * @licence GNU GPL v2+
 * @author Kai Nissen < kai.nissen@wikimedia.de >
 */
class NewDonationRouteTest extends WebRouteTestCase {

	/** @dataProvider paymentInputProvider */
	public function testGivenPaymentInput_paymentDataIsInitiallyValidated( array $validPaymentInput, string $expectedValidity ): void {
		$client = $this->createClient();
		$client->request(
			'POST',
			'/donation/new',
			$validPaymentInput
		);

		$this->assertContains(
			'Payment data: ' . $expectedValidity,
			$client->getResponse()->getContent()
		);
	}

	public function paymentInputProvider(): array {
		return [
			[
				[
					'betrag_auswahl' => '100',
					'zahlweise' => 'BEZ',
					'periode' => '0'
				],
				'valid'
			],
			[
				[
					'amountGiven' => '123.45',
					'zahlweise' => 'PPL',
					'periode' => 6
				],
				'valid'
			],
			[
				[
					'betrag_auswahl' => '0',
					'zahlweise' => 'PPL',
					'periode' => 6
				],
				'invalid'
			],
			[
				[
					'betrag_auswahl' => '100',
					'zahlweise' => 'BTC',
					'periode' => 6
				],
				'invalid'
			]
		];
	}

	public function testWhenPassingTrackingData_itGetsPassedToThePresenter(): void {
		$client = $this->createClient();
		$client->request(
			'POST',
			'/donation/new',
			[
				'impCount' => 12,
				'bImpCount' => 3
			]
		);

		$response = $client->getResponse()->getContent();
		$this->assertContains( 'Impression Count: 12', $response );
		$this->assertContains( 'Banner Impression Count: 3', $response );
	}

}
