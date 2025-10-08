<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Entities\Produto;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Pager;

class Produtos extends BaseController
{

    private $produtoModel;
    private $categoriaModel;

    public function __construct()
    {
        $this->produtoModel = new \App\Models\ProdutoModel();
        $this->categoriaModel = new \App\Models\CategoriaModel();
    }

    public function index()
    {
        $data = [
            'titulo' => 'Listando os produtos',
            'produtos' => $this->produtoModel->select('produtos.*, categorias.nome AS categoria')
                ->join('categorias', 'categorias.id = produtos.categoria_id')
                ->withDeleted(true)
                ->paginate(10),
            'pager' => $this->produtoModel->pager,
        ];

        return view('Admin/Produtos/index', $data);
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

        $produtos = $this->produtoModel->procurar($this->request->getGet('term'));
        $retorno = [];
        foreach ($produtos as $produto) {
            $data['id'] = $produto->id;
            $data['value'] = $produto->nome;

            $retorno[] = $data;
        }

        return $this->response->setJSON($retorno);
    }


    /**
     * Controlador para visualizar dados do produto
     *
     * @param [type] $id
     * @return void
     */
    public function show($id = null)
    {
        $produto = $this->buscaProdutoOu404($id);
        $data = [
            'titulo' => "Detalhando a produto $produto->nome",
            'produto' => $produto,
        ];
        return view('Admin/produtos/show', $data);
    }


    /**
     * Controlador para mostrar form vazio para um novo cadastro
     *
     * @param [type] $id
     * @return void
     */
    public function criar($id = null)
    {
        $produto = new Produto();
        $data = [
            'titulo' => "Criando uma novo produto",
            'produto' => $produto,
            'categorias' => $this->categoriaModel->where('ativo', true)->findAll()
        ];
        return view('Admin/produtos/criar', $data);
    }

    public function editar($id = null)
    {
        $produto = $this->buscaProdutoOu404($id);

        if ($produto->deletado_em != null) {
            return redirect()->back()->with('info', "A produto $produto->nome encontra-se excluído. Portanto não é possível edita-la");
        }

        $data = [
            'titulo' => "Editando a produto $produto->nome",
            'produto' => $produto,
            'categorias' => $this->categoriaModel->where('ativo', true)->findAll()
        ];
        return view('Admin/produtos/editar', $data);
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

            $produto = $this->buscaProdutoOu404($id);

            if ($produto->deletado_em != null) {
                return redirect()->back()->with('info', "A produto $produto->nome encontra-se excluído. Portanto não é possível edita-la");
            }

            $produto->fill($this->request->getPost());

            if (!$produto->hasChanged()) {
                return redirect()->back()->with('info', 'Não há dados para atualizar');
            }

            if ($this->produtoModel->save($produto)) {
                return redirect()
                    ->to(site_url("admin/produtos/show/$produto->id"))
                    ->with('sucesso', "produto $produto->nome atualizada com sucesso.");
            } else {
                return redirect()->back()
                    ->with('errors_model', $this->produtoModel->errors())
                    ->with('atencao', 'Por favor verifique os erros abaixo.')
                    ->withInput();
            }
        } else {
            return redirect()->back();
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

            $produto = new produto($this->request->getPost());

            if ($this->produtoModel->save($produto)) {
                return redirect()
                    ->to(site_url("admin/produtos/show/" . $this->produtoModel->getInsertID()))
                    ->with('sucesso', "produto $produto->nome cadastrada com sucesso.");
            } else {
                return redirect()->back()
                    ->with('errors_model', $this->produtoModel->errors())
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
        $produto = $this->buscaProdutoOu404($id);

        if ($produto->deletado_em != null) {
            return redirect()->back()->with('info', "A produto $produto->nome encontra-se excluída.");
        }

        if ($this->request->getMethod() === 'POST') {
            $this->produtoModel->delete($id);
            return redirect()->to(site_url('admin/produtos'))->with('sucesso', "produto <strong>$produto->nome</strong> excluida com sucesso");
        }

        $data = [
            'titulo' => "Excluindo a produto $produto->nome",
            'produto' => $produto,
        ];
        return view('Admin/produtos/excluir', $data);
    }



    /**
     * Controlador para desfazer a exclusão do usuário
     *
     * @param [type] $id
     * @return void
     */
    public function desfazerExclusao($id = null)
    {
        $produto = $this->buscaProdutoOu404($id);

        if ($produto->deletado_em == null) {
            return redirect()->back()->with('info', 'Apenas produtos excluidas podem ser recuperados');
        }

        if ($this->produtoModel->desfazerExclusao($id)) {
            return redirect()->back()->with('sucesso', 'Exclusão desfeita com sucesso');
        } else {
            return redirect()->back()
                ->with('errors_model', $this->produtoModel->errors())
                ->with('atencao', 'Por favor verifique os erros abaixo.')
                ->withInput();
        }
    }

    public function editarimagem($id = null)
    {
        $produto = $this->buscaProdutoOu404($id);

        $data = [
            'titulo' => "Editando imagem do produto $produto->nome",
            'produto' => $produto,
        ];

        return view('Admin/Produtos/editar_imagem', $data);
    }

    public function upload($id = null)
    {
        $produto = $this->buscaProdutoOu404($id);

        $imagem = $this->request->getFile('foto_produto');

        if (!$imagem->isValid()) {
            $codigoErro = $imagem->getError();

            if ($codigoErro == UPLOAD_ERR_NO_FILE) {
                return redirect()->back()->with('atencao', 'Nenhum arquivo foi selecionado');
            }
        }

        $tamanhoImagem = $imagem->getSizeByUnit('mb');

        if ($tamanhoImagem > 2) {
            return redirect()->back()->with('atencao', 'O arquivo selecionado é muito grande.');
        }

        $tipoImagem = $imagem->getMimeType();

        $tipoImagemLimpo = explode('/', $tipoImagem);

        $tiposPermitidos = [
            'jpeg',
            'png',
            'webp',
        ];

        if (!in_array($tipoImagemLimpo[1], $tiposPermitidos)) {
            return redirect()->back()->with('atencao', 'O arquivo não tem formato válido. Apenas: ' . implode(', ', $tiposPermitidos));
        }

        list($largura, $altura) = getimagesize($imagem->getPathname());

        if ($largura < "400" || $altura < "400") {
            return redirect()->back()->with('atencao', 'A imagem não pode ser menor que 400x400px');
        }

        $imagemCaminho = $imagem->store('produtos');

        $imagemCaminho = WRITEPATH . 'uploads/' . $imagemCaminho;

        service('image')
            ->withFile($imagemCaminho)
            ->fit(400, 400, 'center')
            ->save($imagemCaminho);

        $imagemAntiga = $produto->imagem;

        $produto->imagem = $imagem->getName();

        $this->produtoModel->save($produto);

        $caminhoImagem = WRITEPATH . 'uploads/produtos/' . $imagemAntiga;

        if (is_file($caminhoImagem)) {
            unlink($caminhoImagem);
        }

        return redirect()->to(site_url("admin/produtos/show/$produto->id"))->with('sucesso', 'Imagem alterada com sucesso');
    }

    /**
     * Função para mostrar a imagem nos show
     *
     * @param [string] $imagem
     * @return void
     */
    public function imagem($imagem = null)
    {
        if ($imagem) {
            $caminhoImagem = WRITEPATH . 'uploads/produtos/' . $imagem;
            $infoImagem = new \finfo(FILEINFO_MIME);
            $tipoImagem = $infoImagem->file($caminhoImagem);
            header("Content-Type: $tipoImagem");
            header("Content-Length: " . filesize($caminhoImagem));
            readfile($caminhoImagem);
            exit;
        }
    }

    private function buscaprodutoOu404(int|null $id)
    {
        if (!$id || !$produto = $this->produtoModel->select('produtos.*, categorias.nome AS categoria')
            ->join('categorias', 'categorias.id = produtos.categoria_id')
            ->where('produtos.id', $id)
            ->withDeleted(true)
            ->first()) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("Não encontramos o produto $id");
        }

        return $produto;
    }
}
