<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TarificationApiTest extends TestCase
{
    
        use RefreshDatabase;

        public function testCreateObject()
        {
            $response = $this->postJson('api/delivery/tarificationZone', [
                ["id_membre" => 12, "id_zone" =>12,"tarif" =>"2500"],
                ["id_membre"=>12, "id_zone"=>12,"tarif"=>"2500"]
            ]);
    
            $response->assertStatus(200)
                ->assertJson([['message'=>'success'], 200]);
    
            $this->assertDatabaseHas('tarificationlivraison', [
                ["id_membre"=>12, "id_zone"=>12,"tarif"=>"2500"],
                ["id_membre"=>12, "id_zone"=>12,"tarif"=>"2500"]
            ]);
        }
    
}
