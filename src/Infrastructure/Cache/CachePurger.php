<?php

declare( strict_types = 1 );

namespace WMDE\Fundraising\Frontend\Infrastructure\Cache;

/**
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
interface CachePurger {

	/**
	 * @throws CachePurgingException
	 */
	public function purgeCache(): void;

}
