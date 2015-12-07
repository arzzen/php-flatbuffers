<?php
/*
 * Copyright 2015 Google Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace FlatBuffers; 

interface Constants
{
	const SIZEOF_SHORT = 2;
    const SIZEOF_INT = 4;
    const FILE_IDENTIFIER_LENGTH = 4;
    
    const SBYTE = 127;
    const BYTE = 255;
    const SHORT = 32767;
    const USHORT = 65535;
    const INT = 2147483647;
    const UINT = 4294967295;
    const LONG = 9223372036854775807;
    const ULONG = 18446744073709551615;
}