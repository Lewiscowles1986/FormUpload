<?php

namespace imiskolee\Http;

use Psr\Http\Message\RequestInterface;

use GuzzleHttp\Psr7\Stream;

/***
 * RFC http://www.ietf.org/rfc/rfc1867.txt implement for PHP
 *
 * HTML Form表单上传multipart/form-data的PHP实现
 * @author misko_lee
 * @version 0.1.0
 * @email imiskolee@gmail.com
 */
final class FormUpload
{

    const MIME_JPEG = 'image/jpeg';
    const MIME_PNG = 'image/png';
    const MIME_BMP ='image/bmp';
    const MIME_TEXT = 'text/plan';
    const MIME_HTML = 'text/html';
    const CONTENT_TYPE = 'multipart/form-data';

    private $payload = '';
    private $boundary = '';
    
    public function __construct() {
        $this->payload = '';
        $this->boundary = $this->genBoundary();
    }

    public function addPart($name, $value = '', $mimeType = '', $fileName='') {
        $line = '';
        if(!$mimeType){
            $line =sprintf("%s\nContent-Disposition: form-data; name=\"%s\"\n\n%s\n",
                $this->boundary,
                $name,
                $value
            );
        }else{
            if(!$fileName){
                $fileName = rand(1000,9999).rand(1000,9999).rand(1000,9999);
            }
            $line =sprintf("%s\nContent-Disposition: form-data; name=\"%s\"; filename=\"%s\"\nContent-Type: %s\n\n%s\n",
                $this->boundary,
                $name,
                $fileName,
                $mimeType,
                $value
            );
        }
        $this->payload .= $line."\n\r";
    }

    public function getHeader() {
        return self::CONTENT_TYPE.'; boundary='.substr($this->boundary,2,strlen($this->boundary));
    }
    
    private function genBoundary() {
        return '------MISKO_FORM_UPLOAD_BOUNDARY'.rand(1000,9999).rand(1000,9999);
    }
    
    public function getPayload() {
        $stream = fopen('php://memory','w+');
        fwrite($stream, ($this->payload.$this->boundary."\n\r"));
        rewind($stream);
        return new Stream($stream);
    }

    public function submit(RequestInterface $request) {
        return $request
            ->withAddedHeader('Content-type', $this->getHeader())
            ->withBody(
                $this->getPayload()
            )
            ->withMethod('POST');
    }
}
