<?php

namespace Kanuu\Laravel\Tests;

use Illuminate\Database\Eloquent\Model;
use Kanuu\Laravel\Facades\Kanuu;

/**
 * @see \Kanuu\Laravel\Kanuu::getIdentifier
 */
class GetIdentifierTest extends TestCase
{
    /** @test */
    public function it_returns_the_given_identifier_directly_if_it_is_a_string()
    {
        // When we get the identifier of a string.
        $identifier = Kanuu::getIdentifier('some_identifier');

        // Then we return that same string.
        $this->assertSame('some_identifier', $identifier);
    }

    /** @test */
    public function it_returns_the_given_identifier_casted_as_a_string_if_it_is_a_number()
    {
        // When we get the identifier of a number.
        $identifier = Kanuu::getIdentifier(123.456);

        // Then we return that number as a string.
        $this->assertSame('123.456', $identifier);
    }

    /** @test */
    public function it_uses_the_method_get_kanuu_identifier_if_available()
    {
        // Given any object that has the method "getKanuuIdentifier".
        $object = new class() {
            public function getKanuuIdentifier()
            {
                return 'my_object_identifier';
            }
        };

        // When we get the identifier of that object.
        $identifier = Kanuu::getIdentifier($object);

        // Then we return the value of the "getKanuuIdentifier" method.
        $this->assertSame('my_object_identifier', $identifier);
    }

    /** @test */
    public function it_uses_the_method_get_key_if_the_argument_is_an_eloquent_model()
    {
        // Given an instance of a Eloquent model.
        $model = new class() extends Model {
            public function getKey()
            {
                return 'my_model_identifier';
            }
        };

        // When we get the identifier of that model.
        $identifier = Kanuu::getIdentifier($model);

        // Then we return the value of the "getKey" method.
        $this->assertSame('my_model_identifier', $identifier);
    }

    /** @test */
    public function it_uses_the_method_get_kanuu_identifier_if_available_on_an_eloquent_model()
    {
        // Given an instance of a Eloquent model that has the "getKanuuIdentifier" method.
        $model = new class() extends Model {
            public function getKanuuIdentifier()
            {
                return 'identifier_from_getKanuuIdentifier';
            }

            public function getKey()
            {
                return 'identifier_from_getKey';
            }
        };

        // When we get the identifier of that model.
        $identifier = Kanuu::getIdentifier($model);

        // Then we return the value of the "getKanuuIdentifier" method.
        $this->assertSame('identifier_from_getKanuuIdentifier', $identifier);
    }
}
