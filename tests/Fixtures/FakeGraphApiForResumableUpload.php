<?php
/**
 * Copyright 2017 Facebook, Inc.
 *
 * You are hereby granted a non-exclusive, worldwide, royalty-free license to
 * use, copy, modify, and distribute this software in source code or binary
 * form for use in connection with the web services and APIs provided by
 * Facebook.
 *
 * As with any software that integrates with the Facebook platform, your use
 * of this software is subject to the Facebook Developer Principles and
 * Policies [http://developers.facebook.com/policy/]. This copyright notice
 * shall be included in all copies or substantial portions of the software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL
 * THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
 * FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER
 * DEALINGS IN THE SOFTWARE.
 */

namespace Facebook\Tests\Fixtures;

use GuzzleHttp\Psr7\Response;
use Http\Client\HttpClient;
use Psr\Http\Message\{ RequestInterface, ResponseInterface };

class FakeGraphApiForResumableUpload implements HttpClient
{
    public $transferCount = 0;
    private $respondWith = 'SUCCESS';

    public function failOnStart()
    {
        $this->respondWith = 'FAIL_ON_START';
    }

    public function failOnTransfer()
    {
        $this->respondWith = 'FAIL_ON_TRANSFER';
    }

    public function sendRequest(RequestInterface $request) : ResponseInterface
    {
        $body = $request->getBody()->__toString();
        // Could be start, transfer or finish
        if (strpos($body, 'transfer') !== false) {
            return $this->respondTransfer();
        } elseif (strpos($body, 'finish') !== false) {
            return $this->respondFinish();
        }

        return $this->respondStart();
    }

    private function respondStart()
    {
        if ($this->respondWith == 'FAIL_ON_START') {
            return new Response(
                500,
                ['Foo' => 'Bar'],
                '{"error":{"message":"Error validating access token: Session has expired on Monday, '.
                '10-Aug-15 01:00:00 PDT. The current time is Monday, 10-Aug-15 01:14:23 PDT.",'.
                '"type":"OAuthException","code":190,"error_subcode":463}}'
            );
        }

        return new Response(
            200,
            ['Foo' => 'Bar'],
            '{"video_id":"1337","start_offset":"0","end_offset":"20","upload_session_id":"42"}'
        );
    }

    private function respondTransfer()
    {
        if ($this->respondWith == 'FAIL_ON_TRANSFER') {
            return new Response(
                500,
                ['Foo' => 'Bar'],
                '{"error":{"message":"There was a problem uploading your video. Please try uploading it again.",'.
                '"type":"ApiException","code":6000,"error_subcode":1363019}}'
            );
        }

        switch ($this->transferCount) {
            case 0:
                $data = ['start_offset' => 20, 'end_offset' => 40];
                break;
            case 1:
                $data = ['start_offset' => 40, 'end_offset' => 50];
                break;
            default:
                $data = ['start_offset' => 50, 'end_offset' => 50];
                break;
        }

        $this->transferCount++;

        return new Response(200, ['Foo' => 'Bar'], json_encode($data));
    }

    private function respondFinish()
    {
        return new Response(500, ['Foo' => 'Bar'], '{"success":true}');
    }
}
