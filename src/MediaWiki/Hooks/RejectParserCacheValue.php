<?php

namespace SMW\MediaWiki\Hooks;

use SMW\SQLStore\QueryDependency\DependencyLinksValidator;
use Onoi\EventDispatcher\EventDispatcherAwareTrait;
use SMW\DIWikiPage;
use Title;

/**
 * @see https://www.mediawiki.org/wiki/Manual:Hooks/RejectParserCacheValue
 *
 * @license GNU GPL v2+
 * @since 3.0
 *
 * @author mwjames
 */
class RejectParserCacheValue extends HookHandler {

	use EventDispatcherAwareTrait;

	/**
	 * @var DependencyLinksValidator
	 */
	private $dependencyLinksValidator;

	/**
	 * @since 3.0
	 *
	 * @param DependencyLinksValidator $dependencyLinksValidator
	 */
	public function __construct( DependencyLinksValidator $dependencyLinksValidator ) {
		$this->dependencyLinksValidator = $dependencyLinksValidator;
	}

	/**
	 * @since 3.0
	 *
	 * @param Title $title
	 *
	 * @return boolean
	 */
	public function process( Title $title ) {

		$subject = DIWikiPage::newFromTitle( $title );

		if ( $this->dependencyLinksValidator->hasArchaicDependencies( $subject ) === false ) {
			return true;
		}

		$context = [
			'context' => 'RejectParserCacheValue',
			'subject' => $subject->getHash(),
			'dependency_list' => $this->dependencyLinksValidator->getCheckedDependencies()
		];

		$this->eventDispatcher->dispatch( 'InvalidateResultCache', $context );

		// Return false to reject an otherwise usable cached value from the
		// parser cache
		return false;
	}

}
