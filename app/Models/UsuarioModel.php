<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Libraries\Token;

class UsuarioModel extends Model
{
    protected $table                = 'usuarios';
    protected $returnType           = 'App\Entities\Usuario';
    protected $primaryKey           = 'id';
    protected $allowedFields        = [
        'nome',
        'email',
        'cpf',
        'telefone',
        'reset_hash',
        'reset_expira_em',
        'password',
    ];

    //Datas
    protected $useTimestamps        = true;
    protected $createdField         = 'criado_em';
    protected $updatedField         = 'atualizado_em';
    protected $dateFormat           = 'datetime';
    protected $useSoftDeletes       = true;
    protected $deletedField         = 'deletado_em';

    //Validações
    protected $validationRules = [
        'nome'                  => 'required|min_length[4]|max_length[120]',
        'email'                 => 'required|valid_email|is_unique[usuarios.email]',
        'cpf'                   => 'required|is_unique[usuarios.cpf]|validaCpf',
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

    protected $beforeInsert = ['hashPassword'];
    protected $beforeUpdate = ['hashPassword'];

    protected function hashPassword(array $data)
    {
        if (isset($data['data']['password'])) {
            $data['data']['password_hash'] = password_hash($data['data']['password'], PASSWORD_DEFAULT);
            unset($data['data']['password']);
            unset($data['data']['password_confirmation']);
        }
        return $data;
    }

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
            ->withDeleted(true)
            ->get()
            ->getResult();
    }

    public function desabilitaValidacaoSenha()
    {
        unset($this->validationRules['password']);
        unset($this->validationRules['password_confirmation']);
    }

    public function desfazerExclusao(int $id)
    {
        return $this->protect(false)
            ->where('id', $id)
            ->set('deletado_em', null)
            ->update();
    }

    public function buscaUsuarioPorEmail(string $email)
    {
        return $this->where('email', $email)->first();
    }

    public function buscaUsuarioParaResetarSenha(string $token)
    {
        $token = new Token($token);
        $tokenHash = $token->getHash();

        $usuario = $this->where('reset_hash', $tokenHash)->first();
        if ($usuario != null) {
            if ($usuario->reset_expira_em < date('Y-m-d H:i:s')) {
                $usuario = null;
            }
            return $usuario;
        }
    }
}
