'use strict';

var _ = require( 'underscore' ),
	countrySpecifics = {
		generic: {
			postCode: {
				'data-pattern': '{1,}',
				placeholder: 'z. B. 10117',
				title: 'Postleitzahl'
			},
			city: {
				placeholder: 'z. B. Berlin'
			},
			email: {
				placeholder: 'z. B. name@domain.com'
			}
		},
		DE: {
			postCode: {
				'data-pattern': '\\s*[0-9]{5}\\s*',
				placeholder: 'z. B. 10117',
				title: 'Fünfstellige Postleitzahl'
			},
			city: {
				placeholder: 'z. B. Berlin'
			},
			email: {
				placeholder: 'z. B. name@domain.de'
			}
		},
		AT: {
			postCode: {
				'data-pattern': '\\s*[1-9][0-9]{3}\\s*',
				placeholder: 'z. B. 4020',
				title: 'Vierstellige Postleitzahl'
			},
			city: {
				placeholder: 'z. B. Linz'
			},
			email: {
				placeholder: 'z. B. name@domain.at'
			}
		},
		CH: {
			postCode: {
				'data-pattern': '\\s*[1-9][0-9]{3}\\s*',
				placeholder: 'z. B. 3556',
				title: 'Vierstellige Postleitzahl'
			},
			city: {
				placeholder: 'z. B. Trub'
			},
			email: {
				placeholder: 'z. B. name@domain.ch'
			}
		}
	};

module.exports = {
	getCountrySpecifics: function ( countryCode ) {
		if ( countryCode && _.has( countrySpecifics, countryCode ) ) {
			return countrySpecifics[ countryCode ];
		}

		return countrySpecifics.generic;
	}

};