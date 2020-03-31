<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Wallet;
use App\Transfer;

class TransferTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testPostTransfer()
    {
        $wallet = factory(Wallet::class)->create();
        $transfer = factory(Transfer::class)->create();

        $response = $this->json('POST','/api/transfer',[

            'description' => $transfer->amount,
            'amount' => $transfer->amount,
            'wallet_id' => $wallet->id
        ]);
        $response->assertJsonStructure([
                'id','description','amount','wallet_id'
            ])->assertStatus(201);

        $this->assertDatabaseHas('transfers', [
            'description' => $transfer->amount,
            'amount' => $transfer->amount,
            'wallet_id' => $wallet->id
        ]);

        $this->assertDatabaseHas('wallets', [
            'id' => $wallet->id,
            'money' => $wallet->money + $transfer->amount
            ]);

        $this->assertCount(3,$response->json()['transfers']);
    }
}
