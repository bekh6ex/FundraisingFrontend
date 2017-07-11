<?php

declare( strict_types = 1 );

namespace WMDE\Fundraising\Frontend\Tests\EdgeToEdge\Routes;

use WMDE\Fundraising\Frontend\Tests\EdgeToEdge\WebRouteTestCase;

/**
 * @covers WMDE\Fundraising\Frontend\Presentation\Presenters\IbanPresenter
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 *
 * @requires extension konto_check
 */
class CheckIbanRouteTest extends WebRouteTestCase {

	public function testGivenInvalidBankAccountData_failureResponseIsReturned(): void {
		$client = $this->createClient();

		$client->request(
			'GET',
			'/check-iban',
			[
				'iban' => 'not a valid IBAN!',
			]
		);

		$this->assertJsonSuccessResponse(
			[ 'status' => 'ERR' ],
			$client->getResponse()
		);
	}

	public function testGivenBlockedBankAccountData_failureResponseIsReturned(): void {
		$client = $this->createClient();

		$client->request(
			'GET',
			'/check-iban',
			[
				'iban' => 'wait, this is my own IBAN!',
			]
		);

		$this->assertJsonSuccessResponse(
			[ 'status' => 'ERR' ],
			$client->getResponse()
		);
	}

	public function testGivenValidBankAccountData_successResponseIsReturned(): void {
		$client = $this->createClient();

		$client->request(
			'GET',
			'/check-iban',
			[
				'iban' => 'DE76200505501015754243',
			]
		);

		$this->assertJsonSuccessResponse(
			[
				'status' => 'OK',
				'bic' => 'HASPDEHHXXX',
				'iban' => 'DE76200505501015754243',
				'account' => '1015754243',
				'bankCode' => '20050550',
				'bankName' => 'Hamburger Sparkasse',
			],
			$client->getResponse()
		);
	}

	public function testGivenValidBankAccountDataOfNonGermanBank_successResponseIsReturned(): void {
		$client = $this->createClient();

		$client->request(
			'GET',
			'/check-iban',
			[
				'iban' => 'AT022050302101023600',
			]
		);

		$this->assertJsonSuccessResponse(
			[
				'status' => 'OK',
				'iban' => 'AT022050302101023600',
			],
			$client->getResponse()
		);
	}

}
