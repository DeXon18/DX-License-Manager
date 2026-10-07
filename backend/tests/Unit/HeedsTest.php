<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\Licensing\HeedsService;

class HeedsTest extends TestCase
{
    private HeedsService $heedsService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->heedsService = new HeedsService();
    }

    public function test_it_extracts_minimum_expiration_date()
    {
        $content = "SERVER srv1 ANY 1999\n" .
                   "VENDOR RCTECH\n" .
                   "INCREMENT f1 RCTECH 2026.06 15-oct-2027 1 SIGN=\"AAA\"\n" .
                   "INCREMENT f2 RCTECH 2026.06 10-jan-2026 1 SIGN=\"BBB\"\n" .
                   "INCREMENT f3 RCTECH 2026.06 permanent 1 SIGN=\"CCC\"\n";

        $meta = $this->heedsService->extractMetadata($content);
        $this->assertEquals('10-jan-2026', $meta['expiration']);
    }

    public function test_it_extracts_permanent_when_all_permanent()
    {
        $content = "SERVER srv1 ANY 1999\n" .
                   "VENDOR RCTECH\n" .
                   "INCREMENT f1 RCTECH 2026.06 permanent 1 SIGN=\"AAA\"\n";

        $meta = $this->heedsService->extractMetadata($content);
        $this->assertEquals('permanent', $meta['expiration']);
    }

    public function test_it_ignores_dummy_server_id_and_ancient_dates()
    {
        $content = "SERVER srv1 ANY 1999\n" .
                   "VENDOR RCTECH\n" .
                   "INCREMENT server_id RCTECH 0.1 01-jan-0000 0 SIGN=\"DUMMY\"\n" .
                   "INCREMENT f1 RCTECH 2026.06 15-oct-2027 1 SIGN=\"AAA\"\n" .
                   "INCREMENT f2 RCTECH 2026.06 20-aug-2026 1 SIGN=\"BBB\"\n";

        $meta = $this->heedsService->extractMetadata($content);
        $this->assertEquals('20-aug-2026', $meta['expiration']);
    }
}
