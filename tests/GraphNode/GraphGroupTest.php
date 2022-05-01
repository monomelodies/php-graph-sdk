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
namespace Facebook\Tests\GraphNode;

use Facebook\Response;
use Facebook\GraphNode\GraphNodeFactory;
use Facebook\GraphNode\GraphLocation;
use Facebook\GraphNode\GraphCoverPhoto;
use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\ObjectProphecy;
use Prophecy\PhpUnit\ProphecyTrait;

class GraphGroupTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @var ObjectProphecy|Response
     */
    protected $responseMock;

    protected function setUp() : void
    {
        $this->responseMock = $this->prophesize(Response::class);
    }

    public function testCoverGetsCastAsGraphCoverPhoto()
    {
        $dataFromGraph = [
            'cover' => ['id' => '1337']
        ];

        $this->responseMock->getDecodedBody()->willReturn($dataFromGraph);
        $factory = new GraphNodeFactory($this->responseMock->reveal());
        $graphNode = $factory->makeGraphGroup();

        $cover = $graphNode->getCover();
        $this->assertInstanceOf(GraphCoverPhoto::class, $cover);
    }

    public function testVenueGetsCastAsGraphLocation()
    {
        $dataFromGraph = [
            'venue' => ['id' => '1337']
        ];

        $this->responseMock->getDecodedBody()->willReturn($dataFromGraph);
        $factory = new GraphNodeFactory($this->responseMock->reveal());
        $graphNode = $factory->makeGraphGroup();

        $venue = $graphNode->getVenue();
        $this->assertInstanceOf(GraphLocation::class, $venue);
    }
}
