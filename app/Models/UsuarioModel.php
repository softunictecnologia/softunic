<?php

namespace App\Models;

use CodeIgniter\Model;

class UsuarioModel extends Model
{
    protected $table            = 'usuarios';
    protected $returnType       = 'App\Entities\Usuario';
    protected $allowedFields    = ['nome', 'email', 'telefone'];
    protected $useSoftDeletes   = true;
    protected $useTimestamps    = true;
    protected $createdField     = 'criado_em';
    protected $updatedField     = 'atualizado_em';
    protected $deletedField     = 'deletado_em';

    protected $validationRules = [
        'nome'                  => 'required|min_length[4]|max_length[120]',
        'email'                 => 'required|valid_email|is_unique[usuarios.email]',
        'cpf'                   => 'required|exact_lenght[14]|is_unique[usuarios.cpf]',
        'password'              => 'required|min_length[6]',
        'password_confirmation' => 'required_with[password]|matches[password]',
    ];
    protected $validationMessages = [
        'nome' => [
            'required' => 'Este campo é obrigatório.',
        ],
        'email' => [
            'required' => 'Este campo é obrigatório.',
            'is_unique' => 'Desculpe. Este email já existe.',
        ],
        'cpf' => [
            'required' => 'Este campo é obrigatório.',
            'is_unique' => 'Desculpe. Este CPF já existe.',
        ],
    ];

    /**
     * @uso Controller usuarios no metodo procurar com o autocomplete
     * @param string $term
     * @return array usuarios
     */
    public function procurar($term)
    {
        if ($term === null) {
            return [];
        }
        return $this->select('id, nome')
            ->like('nome', $term)
            ->get()
            ->getResult();
    }
}
