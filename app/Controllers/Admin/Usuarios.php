<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UsuarioModel;
use CodeIgniter\HTTP\ResponseInterface;
use App\Entities\Usuario;

class Usuarios extends BaseController
{

    private $usuarioModel;

    public function __construct()
    {
        $this->usuarioModel = new \App\Models\UsuarioModel();
    }

    public function index()
    {
        $data = [
            'titulo' => 'Listando os usuários',
            'usuarios' => $this->usuarioModel->withDeleted(true)->paginate(2),
            'pager' => $this->usuarioModel->pager,
        ];

        return view('Admin/Usuarios/index', $data);
    }

    public function procurar()
    {
        if (!$this->request->isAJAX()) {
            exit('Pagina não encontrada');
        }

        $usuarios = $this->usuarioModel->procurar($this->request->getGet('term'));
        $retorno = [];
        foreach ($usuarios as $usuario) {
            $data['id'] = $usuario->id;
            $data['value'] = $usuario->nome;

            $retorno[] = $data;
        }

        return $this->response->setJSON($retorno);
    }

    public function show($id = null)
    {
        $usuario = $this->buscaUsuarioOu404($id);
        $data = [
            'titulo' => "Detalhando o usuário $usuario->nome",
            'usuario' => $usuario,
        ];
        return view('Admin/Usuarios/show', $data);
    }

    public function criar($id = null)
    {
        $usuario = new Usuario();
        $data = [
            'titulo' => "Criando um novo usuário",
            'usuario' => $usuario,
        ];
        return view('Admin/Usuarios/criar', $data);
    }

    public function editar($id = null)
    {
        $usuario = $this->buscaUsuarioOu404($id);

        if ($usuario->deletado_em != null) {
            return redirect()->back()->with('info', "O usuário $usuario->nome encontra-se excluído. Portanto não é possível edita-lo");
        }

        $data = [
            'titulo' => "Editando o usuário $usuario->nome",
            'usuario' => $usuario,
        ];
        return view('Admin/Usuarios/editar', $data);
    }

    public function atualizar($id = null)
    {
        if ($this->request->getMethod(true) === 'POST') {

            $usuario = $this->buscaUsuarioOu404($id);

            if ($usuario->deletado_em != null) {
                return redirect()->back()->with('info', "O usuário $usuario->nome encontra-se excluído. Portanto não é possível edita-lo");
            }            

            $post = $this->request->getPost();

            if (empty($post['password'])) {
                $this->usuarioModel->desabilitaValidacaoSenha();
                unset($post['password']);
                unset($post['password_confirmation']);
            }

            $usuario->fill($post);

            if (!$usuario->hasChanged()) {
                return redirect()->back()->with('info', 'Não há dados para atualizar');
            }

            if ($this->usuarioModel->protect(false)->save($usuario)) {
                return redirect()
                    ->to(site_url("admin/usuarios/show/$usuario->id"))
                    ->with('sucesso', "Usuário $usuario->nome atualizado com sucesso.");
            } else {
                return redirect()->back()
                    ->with('errors_model', $this->usuarioModel->errors())
                    ->with('atencao', 'Por favor verifique os erros abaixo.')
                    ->withInput();
            }
        } else {
            return redirect()->back();
        }
    }

    public function excluir($id = null)
    {
        $usuario = $this->buscaUsuarioOu404($id);

        if ($usuario->deletado_em != null) {
            return redirect()->back()->with('info', "O usuário $usuario->nome encontra-se excluído.");
        }        

        if ($usuario->is_admin) {
            return redirect()->back()->with('info', 'Não é possível excluir um <b>Administador</b>');
        }

        if ($this->request->getMethod() === 'POST') {
            $this->usuarioModel->delete($id);
            return redirect()->to(site_url('admin/usuarios'))->with('sucesso', "Usuário <strong>$usuario->nome</strong> excluido com sucesso");
        }

        $data = [
            'titulo' => "Excluindo o usuário $usuario->nome",
            'usuario' => $usuario,
        ];
        return view('Admin/Usuarios/excluir', $data);
    }

    public function desfazerExclusao($id = null){
        $usuario = $this->buscaUsuarioOu404($id);
        
        if ($usuario->deletado_em == null) {
            return redirect()->back()->with('info', 'Apenas usuários excluidos podem ser recuperados');
        }    

        if ($this->usuarioModel->desfazerExclusao($id)) {
            return redirect()->back()->with('sucesso', 'Exclusão desfeita com sucesso');
        } else {
            return redirect()->back()
                ->with('errors_model', $this->usuarioModel->errors())
                ->with('atencao', 'Por favor verifique os erros abaixo.')
                ->withInput();            
        }
    }

    public function cadastrar()
    {
        if ($this->request->getMethod(true) === 'POST') {

            $usuario = new Usuario($this->request->getPost());

            if ($this->usuarioModel->protect(false)->save($usuario)) {
                return redirect()
                    ->to(site_url("admin/usuarios/show/" . $this->usuarioModel->getInsertID()))
                    ->with('sucesso', "Usuário $usuario->nome cadastrado com sucesso.");
            } else {
                return redirect()->back()
                    ->with('errors_model', $this->usuarioModel->errors())
                    ->with('atencao', 'Por favor verifique os erros abaixo.')
                    ->withInput();
            }
        } else {
            return redirect()->back();
        }
    }

    private function buscaUsuarioOu404(int|null $id)
    {
        if (!$id || !$usuario = $this->usuarioModel->withDeleted(true)->where('id', $id)->first()) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("Não encontramos o usuário $id");
        }

        return $usuario;
    }
}
