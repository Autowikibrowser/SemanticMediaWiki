<?php

namespace SMW\MediaWiki;

use Title;
use SMW\DIWikiPage;

/**
 * Isolate access to the LinkBatch class which allows to bulk load a list
 * of titles into the LinkCache.
 *
 * @license GNU GPL v2+
 * @since 3.1
 *
 * @author mwjames
 */
class LinkBatch {

	/**
	 * @var []
	 */
	private $log = [];

	/**
	 * @var []
	 */
	private $batch = [];

	/**
	 * @since 3.1
	 *
	 * @param LinkBatch|null $linkBatch
	 */
	public function __construct( \LinkBatch $linkBatch = null ) {
		$this->linkBatch = $linkBatch;
	}

	/**
	 * @since 3.1
	 *
	 * @param $dataItem
	 */
	public function add( $dataItem ) {

		if ( !$dataItem instanceof DIWikiPage || isset( $this->log[$dataItem->getHash()] ) ) {
			return;
		}

		// PHP 5.6! -> PHP 7 $dataItem->getDBKey(){0}
		$dbkey = $dataItem->getDBKey();

		// Avoid "... ParameterAssertionException: Bad value for parameter
		// $dbkey: invalid DB key '_ASK'"
		if ( $dbkey{0} === '_' ) {
			return;
		}

		// Track which have already been registered because \LinkBatch doesn't
		// check for it
		$this->log[$dataItem->getHash()] = true;
		$this->batch[] = $dataItem;
	}

	/**
	 * @since 3.1
	 *
	 * @param DataItem|null|false $dataItem
	 *
	 * @return boolean
	 */
	public function has( $dataItem ) {

		if ( $dataItem instanceof DIWikiPage && isset( $this->log[$dataItem->getHash()] ) ) {
			return true;
		}

		return false;
	}

	/**
	 * @since 3.1
	 */
	public function execute() {

		if ( $this->linkBatch === null ) {
			$this->linkBatch = new \LinkBatch();
		}

		foreach ( $this->batch as $dataItem ) {
			$this->linkBatch->add( $dataItem->getNamespace(), $dataItem->getDBKey() );
		}

		if ( $this->batch !== [] ) {
			$this->linkBatch->execute();
		}

		$this->batch = [];
	}

}
