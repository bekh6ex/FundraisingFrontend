<?php

declare( strict_types = 1 );

namespace WMDE\Fundraising\Frontend\MembershipContext\UseCases\ApplyForMembership;

use WMDE\Euro\Euro;
use WMDE\Fundraising\Frontend\MembershipContext\Domain\Model\Applicant;
use WMDE\Fundraising\Frontend\MembershipContext\Domain\Model\ApplicantAddress;
use WMDE\Fundraising\Frontend\MembershipContext\Domain\Model\ApplicantName;
use WMDE\Fundraising\Frontend\MembershipContext\Domain\Model\Application;
use WMDE\Fundraising\Frontend\MembershipContext\Domain\Model\EmailAddress;
use WMDE\Fundraising\Frontend\MembershipContext\Domain\Model\Payment;
use WMDE\Fundraising\Frontend\MembershipContext\Domain\Model\PhoneNumber;
use WMDE\Fundraising\Frontend\PaymentContext\Domain\Model\DirectDebitPayment;
use WMDE\Fundraising\Frontend\PaymentContext\Domain\Model\PaymentMethod;
use WMDE\Fundraising\Frontend\PaymentContext\Domain\Model\PaymentType;
use WMDE\Fundraising\Frontend\PaymentContext\Domain\Model\PayPalData;
use WMDE\Fundraising\Frontend\PaymentContext\Domain\Model\PayPalPayment;

/**
 * @license GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class MembershipApplicationBuilder {

	const COMPANY_APPLICANT_TYPE = 'firma';

	public function newApplicationFromRequest( ApplyForMembershipRequest $request ): Application {
		return Application::newApplication(
			$request->getMembershipType(),
			$this->newApplicant( $request ),
			$this->newPayment( $request )
		);
	}

	private function newApplicant( ApplyForMembershipRequest $request ): Applicant {
		return new Applicant(
			$this->newPersonName( $request ),
			$this->newAddress( $request ),
			new EmailAddress( $request->getApplicantEmailAddress() ),
			new PhoneNumber( $request->getApplicantPhoneNumber() ),
			( $request->getApplicantDateOfBirth() === '' ) ? null : new \DateTime( $request->getApplicantDateOfBirth() )
		);
	}

	private function newPersonName( ApplyForMembershipRequest $request ): ApplicantName {
		if ( $request->isCompanyApplication() ) {
			return $this->newCompanyPersonName( $request );
		} else {
			return $this->newPrivatePersonName( $request );
		}
	}

	private function newPrivatePersonName( ApplyForMembershipRequest $request ): ApplicantName {
		$personName = ApplicantName::newPrivatePersonName();
		$personName->setFirstName( $request->getApplicantFirstName() );
		$personName->setLastName( $request->getApplicantLastName() );
		$personName->setSalutation( $request->getApplicantSalutation() );
		$personName->setTitle( $request->getApplicantTitle() );
		return $personName->freeze()->assertNoNullFields();
	}

	private function newCompanyPersonName( ApplyForMembershipRequest $request ): ApplicantName {
		$personName = ApplicantName::newCompanyName();
		$personName->setCompanyName( $request->getApplicantCompanyName() );
		return $personName->freeze()->assertNoNullFields();
	}

	private function newAddress( ApplyForMembershipRequest $request ): ApplicantAddress {
		$address = new ApplicantAddress();

		$address->setCity( $request->getApplicantCity() );
		$address->setCountryCode( $request->getApplicantCountryCode() );
		$address->setPostalCode( $request->getApplicantPostalCode() );
		$address->setStreetAddress( $request->getApplicantStreetAddress() );

		return $address->freeze()->assertNoNullFields();
	}

	private function newPayment( ApplyForMembershipRequest $request ): Payment {
		return new Payment(
			$request->getPaymentIntervalInMonths(),
			Euro::newFromString( $request->getPaymentAmountInEuros() ),
			$this->newPaymentMethod( $request )
		);
	}

	private function newPaymentMethod( ApplyForMembershipRequest $request ): PaymentMethod {
		if ( $request->getPaymentType() === PaymentType::DIRECT_DEBIT ) {
			return new DirectDebitPayment( $request->getBankData() );
		}

		if ( $request->getPaymentType() === PaymentType::PAYPAL ) {
			return new PayPalPayment( new PayPalData() );
		}

		throw new \RuntimeException( 'Unsupported payment type' );
	}

}
