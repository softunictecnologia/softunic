<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\CategoriaModel;
use App\Entities\Categoria;

class Categorias extends BaseController
{

    private $categoriaModel;

    public function __construct()
    {
        $this->categoriaModel = new CategoriaModel();
    }

    public function index()
    {
        $data = [
            'titulo' => 'Listando as Categorias',
            'categorias' => $this->categoriaModel->withDeleted(true)->paginate(10),
            'pager' => $this->categoriaModel->pager,
        ];

        return view('Admin/Categorias/index', $data);
    }

    /**
     * Controlador do campo busca na página principal
     *
     * @return void
     */
    public function procurar()
    {
        if (!$this->request->isAJAX()) {
            exit('Pagina não encontrada');
        }

        $categorias = $this->categoriaModel->procurar($this->request->getGet('term'));
        $retorno = [];
        foreach ($categorias as $categoria) {
            $data['id'] = $categoria->id;
            $data['value'] = $categoria->nome;

            $retorno[] = $data;
        }

        return $this->response->setJSON($retorno);
    }

    /**
     * Controlador para visualizar dados do categoria
     *
     * @param [type] $id
     * @return void
     */
    public function show($id = null)
    {
        $categoria = $this->busCacategoriaOu404($id);
        $data = [
            'titulo' => "Detalhando a categoria $categoria->nome",
            'categoria' => $categoria,
        ];
        return view('Admin/categorias/show', $data);
    }


    /**
     * Controlador para mostrar form vazio para um novo cadastro
     *
     * @param [type] $id
     * @return void
     */
    public function criar($id = null)
    {
        $categoria = new Categoria();
        $data = [
            'titulo' => "Criando uma nova categoria",
            'categoria' => $categoria,
        ];
        return view('Admin/Categorias/criar', $data);
    }


    public function editar($id = null)
    {
        $categoria = $this->buscaCategoriaOu404($id);

        if ($categoria->deletado_em != null) {
            return redirect()->back()->with('info', "A categoria $categoria->nome encontra-se excluído. Portanto não é possível edita-la");
        }

        $data = [
            'titulo' => "Editando a categoria $categoria->nome",
            'categoria' => $categoria,
        ];
        return view('Admin/categorias/editar', $data);
    }

    /**
     * Controlador para Atualizar os dados no banco de dados
     *
     * @param [type] $id
     * @return void
     */
    public function atualizar($id = null)
    {
        if ($this->request->getMethod(true) === 'POST') {

            $categoria = $this->buscaCategoriaOu404($id);

            if ($categoria->deletado_em != null) {
                return redirect()->back()->with('info', "A categoria $categoria->nome encontra-se excluído. Portanto não é possível edita-la");
            }

            $categoria->fill($this->request->getPost());

            if (!$categoria->hasChanged()) {
                return redirect()->back()->with('info', 'Não há dados para atualizar');
            }

            if ($this->categoriaModel->save($categoria)) {
                return redirect()
                    ->to(site_url("admin/categorias/show/$categoria->id"))
                    ->with('sucesso', "Categoria $categoria->nome atualizada com sucesso.");
            } else {
                return redirect()->back()
                    ->with('errors_model', $this->categoriaModel->errors())
                    ->with('atencao', 'Por favor verifique os erros abaixo.')
                    ->withInput();
            }
        } else {
            return redirect()->back();
        }
    }

    /**
     * Controlador para excluir o usuário selecionado
     *
     * @param [type] $id
     * @return void
     */
    public function excluir($id = null)
    {
        $categoria = $this->buscaCategoriaOu404($id);

        if ($categoria->deletado_em != null) {
            return redirect()->back()->with('info', "A categoria $categoria->nome encontra-se excluída.");
        }

        if ($this->request->getMethod() === 'POST') {
            $this->categoriaModel->delete($id);
            return redirect()->to(site_url('admin/categorias'))->with('sucesso', "Categoria <strong>$categoria->nome</strong> excluida com sucesso");
        }

        $data = [
            'titulo' => "Excluindo a categoria $categoria->nome",
            'categoria' => $categoria,
        ];
        return view('Admin/categorias/excluir', $data);
    }



    /**
     * Controlador para desfazer a exclusão do usuário
     *
     * @param [type] $id
     * @return void
     */
    public function desfazerExclusao($id = null)
    {
        $categoria = $this->buscaCategoriaOu404($id);

        if ($categoria->deletado_em == null) {
            return redirect()->back()->with('info', 'Apenas categorias excluidas podem ser recuperados');
        }

        if ($this->categoriaModel->desfazerExclusao($id)) {
            return redirect()->back()->with('sucesso', 'Exclusão desfeita com sucesso');
        } else {
            return redirect()->back()
                ->with('errors_model', $this->categoriaModel->errors())
                ->with('atencao', 'Por favor verifique os erros abaixo.')
                ->withInput();
        }
    }


    /**
     * Controlador para cadastrar um novo usuário
     *
     * @return void
     */
    public function cadastrar()
    {
        if ($this->request->getMethod(true) === 'POST') {

            $categoria = new Categoria($this->request->getPost());

            if ($this->categoriaModel->save($categoria)) {
                return redirect()
                    ->to(site_url("admin/categorias/show/" . $this->categoriaModel->getInsertID()))
                    ->with('sucesso', "Categoria $categoria->nome cadastrada com sucesso.");
            } else {
                return redirect()->back()
                    ->with('errors_model', $this->categoriaModel->errors())
                    ->with('atencao', 'Por favor verifique os erros abaixo.')
                    ->withInput();
            }
        } else {
            return redirect()->back();
        }
    }

    private function buscaCategoriaOu404(int|null $id)
    {
        if (!$id || !$categoria = $this->categoriaModel->withDeleted(true)->where('id', $id)->first()) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("Não encontramos o usuário $id");
        }

        return $categoria;
    }
}
