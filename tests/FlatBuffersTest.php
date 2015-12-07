<?php
use FlatBuffers\FlatBufferBuilder;
use FlatBuffers\ByteBuffer;

class FlatBuffersTest extends PHPUnit_Framework_TestCase
{
	
	public function testBuilder()
	{
		$fbb = new FlatBufferBuilder(1);
		
		$str = $fbb->createString("test text");
		
		$fred = $fbb->createString('Fred');
		
		$test4 = $fbb->endVector();
		
		print_r( $fbb->dataBuffer() );
	}
	
}