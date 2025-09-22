<?php

namespace App\Models;

use CodeIgniter\Model;

class UsuarioModel extends Model
{
    protected $table                = 'usuarios';
    protected $returnType           = 'App\Entities\Usuario';
    protected $allowedFields        = ['nome', 'email', 'cpf', 'telefone',];
    protected $useSoftDeletes       = true;
    protected $useTimestamps        = true;
    protected $createdField         = 'criado_em';
    protected $updatedField         = 'atualizado_em';
    protected $deletedField         = 'deletado_em';
    protected bool $updateOnlyChanged = false;

    protected $validationRules = [
        'nome'                  => 'required|min_length[4]|max_length[120]',
        'email'                 => 'required|valid_email|is_unique[usuarios.email]',
        'cpf'                   => 'required|is_unique[usuarios.cpf]',
        'telefone'              => 'required|max_length[20]',
        'password'              => 'required|min_length[6]',
        'password_confirmation' => 'required_with[password]|matches[password]',
    ];
    protected $validationMessages = [
        'nome' => [
            'required' => 'O campo Nome é obrigatório.',
        ],
        'email' => [
            'required' => 'O campo Email é obrigatório.',
            'is_unique' => 'Desculpe. Este email já existe.',
        ],
        'cpf' => [
            'required' => 'O campo CPF é obrigatório.',
            'is_unique' => 'Desculpe. Este CPF já existe.',
        ],
        'telefone' => [
            'required' => 'O campo Telefone é obrigatório.',
        ],        
        'password' => [
            'required' => 'O campo Senha é obrigatório.',
            'min_length' => 'Campo senha tem que ter no mínimo 6 caracteres',
        ],         
        'password_confirmation' => [
            'required_with' => 'O campo Confirmação de Senha é obrigatório.',
            'matches' => 'As senhas não conferem.',
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

    public function desabilitaValidacaoSenha(){
        unset($this->validationRules['password']);
        unset($this->validationRules['password_confirmation']);
    }
}
