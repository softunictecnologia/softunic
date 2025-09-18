<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UsuarioSeeder extends Seeder
{
    public function run()
    {
        $usuarioModel = new \App\Models\UsuarioModel;

        $usuario = [
            'nome'     => 'Administrador do Sistema',
            'email'    => 'adm@adm.com.br',
            'telefone' => '(49) 3558-0701',
            'cpf' => '025.495.019-18',
        ];

        $usuarioModel->protect(false)->insert($usuario);

        $usuario = [
            'nome'     => 'Daniel Pauly',
            'email'    => 'danielpaulysc@gmail.com',
            'telefone' => '(49) 98914-8914',
            'cpf' => '654.321.987-54',
        ];

        $usuarioModel->protect(false)->insert($usuario);  
        
        dd($usuarioModel->errors());
    }

}
