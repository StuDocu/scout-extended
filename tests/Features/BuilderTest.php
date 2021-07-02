<?php

declare(strict_types=1);

namespace Tests\Features;

use App\User;
use Mockery;
use Tests\TestCase;

final class BuilderTest extends TestCase
{
    public function testCount(): void
    {
        $this->mockEngine()->expects('search')->andReturn([
            'nbHits' => 5,
        ]);

        $this->assertSame(5, User::search('')->count());
    }

    public function testWith(): void
    {
        $this->mockIndex(User::class)->expects('search')->with('foo', Mockery::subset(['aroundRadius' => 1]))->andReturn(['hits' => []]);

        User::search('foo')->with(['aroundRadius' => 1])->get();
    }

    public function testWhereOptional(): void
    {
        $this->mockIndex(User::class)
            ->expects('search')
            ->with('foo', Mockery::subset(['optionalFilters' => 'sub1.id:1,sub2.name:hello']))
            ->andReturn(['hits' => []]);

        User::search('foo')
            ->whereOptional('sub1.id', 1)
            ->whereOptional('sub2.name', 'hello')
            ->get();
    }

    public function testWhereOptionalAndWith(): void
    {
        $this->mockIndex(User::class)
            ->expects('search')
            ->with('foo', Mockery::subset(['optionalFilters' => 'price:100']))
            ->andReturn(['hits' => []]);

        User::search('foo')
            ->whereOptional('sub1.id', 1)
            ->whereOptional('sub2.name', 'hello')
            ->with([
                'optionalFilters' => 'price:100'
            ])
            ->get();
    }

    public function testWithAndWhereOptional(): void
    {
        $this->mockIndex(User::class)
            ->expects('search')
            ->with('foo', Mockery::subset(['optionalFilters' => 'sub1.id:1,sub2.name:hello']))
            ->andReturn(['hits' => []]);

        User::search('foo')
            ->with([
                'optionalFilters' => 'price:100'
            ])
            ->whereOptional('sub1.id', 1)
            ->whereOptional('sub2.name', 'hello')
            ->get();
    }

    public function testAroundLatLng(): void
    {
        $this->mockIndex(User::class)->expects('search')->with('bar', Mockery::subset(['aroundLatLng' => '48.8566,2.3522']))->andReturn(['hits' => []]);

        User::search('bar')->aroundLatLng(48.8566, 2.3522)->get();
    }

    public function testQueryIsString(): void
    {
        $this->assertTrue(User::search(null)->query === '');
    }
}
