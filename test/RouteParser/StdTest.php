<?php

namespace FastRoute\RouteParser;

class StdTest extends \PhpUnit_Framework_TestCase {
    /** @dataProvider provideTestParse */
    public function testParse($routeString, $expectedRouteDatas) {
        $parser = new Std();
        $routeDatas = $parser->parse($routeString);
        $this->assertSame($expectedRouteDatas, $routeDatas);
    }

    /** @dataProvider provideTestParseError */
    public function testParseError($routeString, $expectedExceptionMessage) {
        $parser = new Std();
        $this->setExpectedException('FastRoute\\BadRouteException', $expectedExceptionMessage);
        $parser->parse($routeString);
    }

    public function provideTestParse() {
        return array(
            array(
                '/test',
                array(
                    array('/test'),
                )
            ),
            array(
                '/test/{param}',
                array(
                    array('/test/', array('param', '[^/]+')),
                )
            ),
            array(
                '/te{ param }st',
                array(
                    array('/te', array('param', '[^/]+'), 'st')
                )
            ),
            array(
                '/test/{param1}/test2/{param2}',
                array(
                    array('/test/', array('param1', '[^/]+'), '/test2/', array('param2', '[^/]+'))
                )
            ),
            array(
                '/test/{param:\d+}',
                array(
                    array('/test/', array('param', '\d+'))
                )
            ),
            array(
                '/test/{ param : \d{1,9} }',
                array(
                    array('/test/', array('param', '\d{1,9}'))
                )
            ),
            array(
                '/test[opt]',
                array(
                    array('/test'),
                    array('/testopt'),
                )
            ),
            array(
                '/test[/{param}]',
                array(
                    array('/test'),
                    array('/test/', array('param', '[^/]+')),
                )
            ),
            array(
                '/{param}[opt]',
                array(
                    array('/', array('param', '[^/]+')),
                    array('/', array('param', '[^/]+'), 'opt')
                )
            ),
            array(
                '/test[/{name}[/{id:[0-9]+}]]',
                array(
                    array('/test'),
                    array('/test/', array('name', '[^/]+')),
                    array('/test/', array('name', '[^/]+'), '/', array('id', '[0-9]+')),
                )
            ),
        );
    }

    public function provideTestParseError() {
        return array(
            array(
                '/test[opt',
                "Number of opening '[' and closing ']' does not match"
            ),
            array(
                '/test[optarray[opt2]',
                "Number of opening '[' and closing ']' does not match"
            ),
            array(
                '/testopt]',
                "Number of opening '[' and closing ']' does not match"
            ),
            array(
                '/test[]',
                "Empty optional part"
            ),
            array(
                '/test[[opt]]',
                "Empty optional part"
            ),
        );
    }
}
