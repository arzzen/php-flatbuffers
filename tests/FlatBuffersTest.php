<?php
use FlatBuffers\Table;
use FlatBuffers\FlatBufferBuilder;
use FlatBuffers\ByteBuffer;
use FlatBuffers\Constants;

class StringWrapper extends Table implements Constants
{

	private $fbb;

	public function __construct(FlatBufferBuilder $flatBufferBuilder)
	{
		$this->fbb = $flatBufferBuilder;
	}

    public function tearDown()
    {
        Mockery::close();
    }


    public function init(ByteBuffer $byteBuffer)
	{
		$this->bb = $byteBuffer;
		$this->bb_pos = $this->bb->getInt($this->bb->getPosition()) + $this->bb->getPosition();

		return $this;
	}

	public function getString($slot = 0)
    {
		$vtable_offset = self::SIZEOF_INT + ($slot * 2);

		$vtable = $this->bb_pos - $this->bb->getInt($this->bb_pos);

		$offset = $vtable_offset < $this->bb->getShort($vtable) ? $this->bb->getShort($vtable + $vtable_offset) : 0;

		$offset += $this->bb_pos + $this->bb->getInt($offset + $this->bb_pos);
		$len = $this->bb->getInt($offset);
		$startPos = $offset + self::SIZEOF_INT;
		$_string = substr($this->bb->_buffer, $startPos, $len);

		return ($offset != 0 ? $_string : null);
    }

	public function createString($value)
	{
		return $this->fbb->createString($value);
	}

	public function addString($slot, $str)
	{
		$this->fbb->addOffsetX($slot, $str, 0);
	}

    public function createArrayOfStringVector(array $data)
    {
        $this->fbb->startVector(4, count($data), 4);
        for ($i = count($data) - 1; $i >= 0; $i--)
		{
            $this->fbb->addOffset($data[$i]);
        }

        return $this->fbb->endVector();
    }

	public function dataBuffer()
	{
		return $this->fbb->dataBuffer();
	}

	public function startObject($numfields)
	{
		$this->fbb->startObject($numfields);
	}

	public function endObject()
	{
		return $this->fbb->endObject();
	}

	public function finish($root_table, $identifier = NULL)
	{
		$this->fbb->finish($root_table, $identifier);
	}

}


class FlatBuffersTest extends PHPUnit_Framework_TestCase
{

	public function testCreateString()
	{
		$flatBufferBuilder = new FlatBufferBuilder(1);
		$stringWrapper = new StringWrapper($flatBufferBuilder);

		$firstText = $stringWrapper->createString('first_value');
		$secondText = $stringWrapper->createString('second_value');

		$stringWrapper->startObject(25);
			$stringWrapper->addString(2, $firstText);
			$stringWrapper->addString(3, $secondText);
		$stringWrapper->finish($stringWrapper->endObject());

		$stringWrapper->init($stringWrapper->dataBuffer());

		$this->assertEquals('first_value', $stringWrapper->getString(2));
		$this->assertEquals('second_value', $stringWrapper->getString(3));
	}

	public function testReadDataFromFile()
	{
		$bytes = file_get_contents(dirname((__FILE__)).DIRECTORY_SEPARATOR.'test.data.mon');

		$flatBufferBuilder = new FlatBufferBuilder(1);
		$stringWrapper = new StringWrapper($flatBufferBuilder);

		$this->assertEquals($flatBufferBuilder->bb->_buffer, $stringWrapper->dataBuffer()->data());
	}

    public function lessThanOneDataProvider()
    {
        return array(
            array(0),
            array(-1),
            array(-1000)
        );
    }

    /**
     * @dataProvider lessThanOneDataProvider
     * @param $value
     */
    public function testInitialSizeDefaultsToOneWhenLessThanOne($value)
    {
        $flatBufferBuilder = new FlatBufferBuilder($value);
        $this->assertAttributeEquals(1, 'space', $flatBufferBuilder);
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testPrepThrowsExceptionWhenBufferGrowsBeyond2GB()
    {
        $this->setExpectedException(\Exception::class, "FlatBuffers: cannot grow buffer beyond 2 gigabytes");

        $byteBuffer = Mockery::mock('overload:' . ByteBuffer::class);
        $byteBuffer->shouldReceive('capacity')
            ->andReturn((float)2e+9);

        $flatBufferBuilder = new FlatBufferBuilder(1);
        $flatBufferBuilder->prep(1, 1);
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testPutBoolAddsCharacterToBuffer()
    {
        $byteBuffer = Mockery::mock('overload:' . ByteBuffer::class);
        $byteBuffer->shouldReceive('put')
            ->with(0, chr(1));

        $flatBufferBuilder = new FlatBufferBuilder(1);
        $flatBufferBuilder->putBool(true);

        $byteBuffer->shouldReceive('put')
            ->with(0, chr(0));

        $flatBufferBuilder = new FlatBufferBuilder(1);
        $flatBufferBuilder->putBool(false);
    }
    
    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testPutSbyteAddsCharacterToBuffer()
    {
        $byteBuffer = Mockery::mock('overload:' . ByteBuffer::class);
        $byteBuffer->shouldReceive('put')
            ->with(0, chr(35));

        $flatBufferBuilder = new FlatBufferBuilder(1);
        $flatBufferBuilder->putSbyte(35);
    }

}
