<?php

declare( strict_types = 1 );

namespace WMDE\Fundraising\Frontend\Tests\EdgeToEdge\Routes;

use Silex\Application;
use Symfony\Component\HttpKernel\Client;
use WMDE\Fundraising\Entities\Address;
use WMDE\Fundraising\Entities\Subscription;
use WMDE\Fundraising\Frontend\Factories\FunFunFactory;
use WMDE\Fundraising\Frontend\Tests\EdgeToEdge\WebRouteTestCase;

/**
 * @licence GNU GPL v2+
 * @author Gabriel Birke < gabriel.birke@wikimedia.de >
 */
class ConfirmSubscriptionRouteTest extends WebRouteTestCase {

	// @codingStandardsIgnoreStart
	protected function onTestEnvironmentCreated( FunFunFactory $factory, array $config ) {
		// @codingStandardsIgnoreEnd
	}

	private function newSubscriptionAddress(): Address {
		$address = new Address();
		$address->setSalutation( 'Herr' );
		$address->setFirstName( 'Nyan' );
		$address->setLastName( 'Cat' );
		$address->setTitle( 'Dr.' );
		return $address;
	}

	public function testGivenAnUnconfirmedSubscriptionRequest_successPageIsDisplayed() {

		$this->createAppEnvironment(
			[ ],
			function ( Client $client, FunFunFactory $factory, Application $app ) {

				// @todo Make this the default behaviour of WebRouteTestCase::createAppEnvironment()
				$factory->setTwigEnvironment( $app['twig'] );

				$subscription = new Subscription();
				$subscription->setConfirmationCode( 'deadbeef' );
				$subscription->setEmail( 'tester@example.com' );
				$subscription->setAddress( $this->newSubscriptionAddress() );

				$factory->getSubscriptionRepository()->storeSubscription( $subscription );

				$client->request(
					'GET',
					'/contact/confirm-subscription/deadbeef'
				);
				$response = $client->getResponse();

				$this->assertSame( 200, $response->getStatusCode() );
				$this->assertContains( 'Subscription confirmed.', $response->getContent() );
			}
		);

	}

	public function testGivenANonHexadecimalConfirmationCode_confirmationPageIsNotFound() {
		$client = $this->createClient( [], null, self::DISABLE_DEBUG );

		$client->request(
			'GET',
			'/contact/confirm-subscription/kittens'
		);

		$this->assert404( $client->getResponse() );
	}

	public function testGivenNoSubscription_anErrorIsDisplayed() {
		$client = $this->createClient();

		$client->request(
			'GET',
			'/contact/confirm-subscription/deadbeef'
		);
		$response = $client->getResponse();

		$this->assertSame( 200, $response->getStatusCode() );
		$this->assertContains( 'subscription_confirmation_code_not_found', $response->getContent() );
	}

	public function testGivenAConfirmedSubscriptionRequest_successPageIsDisplayed() {
		$this->createEnvironment( [], function( Client $client, FunFunFactory $factory ) {
			$subscription = new Subscription();
			$subscription->setConfirmationCode( 'deadbeef' );
			$subscription->setEmail( 'tester@example.com' );
			$subscription->setAddress( $this->newSubscriptionAddress() );
			$subscription->markAsConfirmed();

			$factory->getSubscriptionRepository()->storeSubscription( $subscription );

			$client->request(
				'GET',
				'/contact/confirm-subscription/deadbeef'
			);
			$response = $client->getResponse();

			$this->assertSame( 200, $response->getStatusCode() );
			$this->assertContains( 'subscription_already_confirmed', $response->getContent() );
		} );
	}
}
