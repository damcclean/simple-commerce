<?php

namespace Damcclean\Commerce\Tests;

class WebProductControllerTest extends TestCase
{
    /** @test */
    public function can_get_index_of_products()
    {
        dd('hi there!');

        $response = $this->get('/products');

        $response->assertOk();
    }
}
