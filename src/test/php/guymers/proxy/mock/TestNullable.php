<?php

namespace guymers\proxy\mock;

class TestNullable {
	
		public function testingNullableTypeHintParams(?Test $param1Nullable, ?Test $param2Nullable = null) : ?string {
			return "testingNullableTypeHintParams " . $param1->testingParams("1", "2");
		}
	
}
