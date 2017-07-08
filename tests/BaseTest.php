<?php

use PHPUnit\Framework\TestCase;

use GuzzleHttp\Psr7\Request;

use imiskolee\Http\FormUpload;

class BaseTest extends TestCase
{
    public function setup()
    {
        $this->form = new FormUpload();
    }
    
    /**
     * @test
     */
    public function it_accepts_any_valid_psr7_requestinterface_implementation() {
        $request = new Request('GET', 'https://www.github.com');
        $out = $this->form->submit($request);
        $this->assertTrue($out->getMethod() == 'POST');
    }
    
    /**
     * @test
     */
    public function it_does_not_mutate_requests() {
        $request = new Request('GET', 'https://www.github.com');
        $out = $this->form->submit($request);
        $this->assertTrue($request->getMethod() != $out->getMethod());
    }
}
