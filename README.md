### PHP [Memory Efficient Serialization Library](https://github.com/google/flatbuffers)

FlatBuffers is a serialization library for games and other memory constrained apps. 

FlatBuffers allows you to directly access serialized data without unpacking/parsing it first, while still having great forwards/backwards compatibility. 

FlatBuffers can be built for many different systems (Android, Windows, OS X, Linux)

*For testing only* 

### Composer
```
{
    "repositories": [
        {
            "url": "https://github.com/arzzen/php-flatbuffers.git",
            "type": "git"
        }
    ],
    "require": {
        "arzzen/php-flatbuffers": "dev-master"
    }
}
```

### Test:
```ruby
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

```