<?php

namespace SMW\Tests\MediaWiki;

use SMW\MediaWiki\LinkBatch;
use SMW\DIWikiPage;

/**
 * @covers \SMW\MediaWiki\LinkBatch
 * @group semantic-mediawiki
 *
 * @license GNU GPL v2+
 * @since   3.1
 *
 * @author mwjames
 */
class LinkBatchTest extends \PHPUnit_Framework_TestCase {

	public function testCanConstruct() {

		$this->assertInstanceOf(
			LinkBatch::class,
			 new LinkBatch()
		);
	}

	public function testAdd_NoPage() {

		$instance = new LinkBatch();
		$instance->add( 'Foo' );

		$this->assertFalse(
			$instance->has( 'Foo' )
		);
	}

	public function testAdd_PageButRefuseFirstUnderscore() {

		$subject = DIWikiPage::newFromText( '_ASK' );

		$instance = new LinkBatch();
		$instance->add( $subject );

		$this->assertFalse(
			$instance->has( $subject )
		);
	}

	public function testExecute() {

		$subject = DIWikiPage::newFromText( 'Foo' );

		$linkBatch = $this->getMockBuilder( '\LinkBatch' )
			->disableOriginalConstructor()
			->getMock();

		$linkBatch->expects( $this->once() )
			->method( 'add' );

		$linkBatch->expects( $this->once() )
			->method( 'execute' );

		$instance = new LinkBatch( $linkBatch );
		$instance->add( $subject );

		$instance->execute();
	}

}
