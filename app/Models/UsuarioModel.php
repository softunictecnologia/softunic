<?php

namespace App\Models;

use CodeIgniter\Model;

class UsuarioModel extends Model
{
    protected $table            = 'usuarios';
    protected $returnType       = 'object'; 
    protected $useSoftDeletes   = true;
    protected $allowedFields    = ['nome', 'email', 'telefone'];

    protected $useTimestamps        = true;
    protected $createdField         = 'criado_em';
    protected $updatedField         = 'atualizado_em';
    protected $deletedField         = 'deletado_em';

    /**
     * @uso Controller usuarios no metodo procurar com o autocomplete
     * @param string $term
     * @return array usuarios
     */
    public function procurar($term){
        if ($term === null){
            return [];
        }        
        return $this->select('id, nome')
                    ->like('nome', $term)
                    ->get()
                    ->getResult();
    }
}
