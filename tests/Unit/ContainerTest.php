<?php

namespace Tests\Unit;

use App\Container;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerExceptionInterface;

class ContainerTest extends TestCase
{
    private $userService;

    #[Test] public function there_are_no_entries_when_container_is_created()
    {
        $this->assertEmpty((new Container())->getEntries());
    }

    #[Test] public function it_sets_entry()
    {
        $container = new Container();
        $id        = $this->userService::class;


        $container->set($id, fn() => new $this->userService());

        $excepted = [
            $id => fn() => new $this->userService(),
        ];

        $this->assertEquals($excepted, $container->getEntries());
    }

    public function it_throws_container_exception_interface()
    {
        $container = new Container();
        $id        = 'NonExistingClassName';

        $container->set();

        $this->expectException(ContainerExceptionInterface::class);
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->userService = new class () {
        };
    }
}
