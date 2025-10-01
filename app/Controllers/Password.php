<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Entities\Usuario;
use App\Models\UsuarioModel;
use CodeIgniter\HTTP\ResponseInterface;

class Password extends BaseController
{
    private $usuarioModel;

    public function __construct()
    {
        $this->usuarioModel = new \App\Models\UsuarioModel();
    }

    public function esqueci()
    {
        $data = [
            'titulo' => 'Esqueci minha senha',
        ];

        return view('Password/esqueci', $data);
    }

    public function processaesqueci()
    {
        if ($this->request->getMethod() === 'POST') {
            $usuario = $this->usuarioModel->buscaUsuarioPorEmail($this->request->getPost('email'));

            if ($usuario === null || !$usuario->ativo) {
                return redirect()->to(site_url('password/esqueci'))->with('atencao', 'Não encontramos o email');
            }

            $usuario->iniciaPasswordReset();

            $this->usuarioModel->save($usuario);

            $this->enviaEmailRedefinicaoSenha($usuario);

            return redirect()->to(site_url('login'))->with('sucesso', 'Email de redefinição de senha enviado para seu email');
        } else {
            return redirect()->back();
        }
    }

    public function reset($token = null)
    {
        if ($token === null) {
            return redirect()->to(site_url('password/esqueci'))->with('atencao', 'Link inválido ou expirado');
        }

        $usuario = $this->usuarioModel->buscaUsuarioParaResetarSenha($token);

        if ($usuario != null) {
            $data = [
                'titulo' => 'Redefina a sua senha',
                'token' => $token,
            ];

            return view('Password/reset', $data);
        } else {
            return redirect()->to(site_url('password/esqueci'))->with('atencao', 'Link inválido ou expirado');
        }
    }

    public function processaReset($token = null)
    {
        if ($token === null) {
            return redirect()->to(site_url('password/esqueci'))->with('atencao', 'Link inválido ou expirado');
        }

        $usuario = $this->usuarioModel->buscaUsuarioParaResetarSenha($token);

        if ($usuario != null) {

            $usuario->fill($this->request->getPost());


            if ($this->usuarioModel->save($usuario)) {

                $usuario->completaPasswordReset();

                $this->usuarioModel->save($usuario);

                return redirect()->to(site_url("login"))->with('sucesso', 'Senha atualizada com sucesso');
            } else {
                return redirect()->to(site_url("password/reset/$token"))
                    ->with('errors_model', $this->usuarioModel->errors())
                    ->with('atencao', 'Por favor verifique os erros abaixo.')
                    ->withInput();
            }
        } else {
            return redirect()->to(site_url('password/esqueci'))->with('atencao', 'Link inválido ou expirado');
        }
    }


    private function enviaEmailRedefinicaoSenha(object $usuario)
    {

        $email = service('email');

        $email->setFrom('no-reply@softunic.com.br', 'Food Delivery');
        $email->setTo($usuario->email);

        $mensagem = view('Password/reset_email', ['token' => $usuario->reset_token]);

        $email->setSubject('Redefinição de Senha');
        $email->setMessage($mensagem);

        $email->send();
    }
}
