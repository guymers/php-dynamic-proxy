<?php

namespace guymers\proxy\mock;

class TestNullable {
	
		public function testingNullableTypeHintParams(?Test $param1Nullable, ?Test $param2Nullable = null) : ?Test {
			return new Test("");
		}
	
}
