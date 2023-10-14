<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RegisterTest extends TestCase{

    use RefreshDatabase; 
    
    public function testPostEndpoint(){
        // Preparar datos para la solicitud POST con dominio de correo valido
        $data = [
            'name' => 'J Perez',
            'email' => 'j.perez@alumnos.santotomas.cl',
            'password' => 'password12'
        ];
        
        // Realizar la solicitud POST al endpoint de la API
        $response = $this->json('POST', '/api/user/register', $data);
        // Verificar la respuesta de la API
        $response->assertStatus(200);
    }

    public function testPostEndpointWithError(){
        // Preparar datos con error para la solicitud POST
        $data = [
            'name' => 'J Perez',
            'email' => 'j.perez@correo.com',
            'password' => 'password12'
        ];
        // Realizar la solicitud POST al endpoint de la API
        $response = $this->json('POST', '/api/user/register', $data);
        // Verificar la respuesta de la API
        $response->assertStatus(400);
    }
}
