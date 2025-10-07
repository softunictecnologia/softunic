<?php echo $this->extend('Admin/layout/principal'); ?>

<!-- ----------------------------------------------- -->

<?php echo $this->section('titulo'); ?>
<?php echo $titulo; ?>
<?php echo $this->endSection(); ?>

<!-- ----------------------------------------------- -->

<?php echo $this->section('estilos'); ?>

<?php echo $this->endSection(); ?>

<!-- ----------------------------------------------- -->

<?php echo $this->section('conteudo'); ?>

<div class="col-lg-6 grid-margin stretch-card">
  <div class="card">
    <div class="card-header bg-light pb-0 pt-4">
      <h4 class="card-title"><?php echo esc($titulo); ?></h4>
    </div>

    <div class="card-body">

      <p class="card-text">
        <span class="font-weight-bold">Nome: </span>
        <?php echo esc($produto->nome); ?>
      </p>
      <p class="card-text">
        <span class="font-weight-bold">Categoria: </span>
        <?php echo esc($produto->categoria); ?>
      </p>
      <p class="card-text">
        <span class="font-weight-bold">Slug: </span>
        <?php echo esc($produto->slug); ?>
      </p>
      <p class="card-text">
        <span class="font-weight-bold">Ativo: </span>
        <?php echo esc($produto->ativo ? 'Sim' : 'Não'); ?>
      </p>
      <p class="card-text">
        <span class="font-weight-bold">Criado: </span>
        <?php echo $produto->criado_em->humanize(); ?>
      </p>

      <?php if ($produto->deletado_em == null): ?>
        <p class="card-text">
          <span class="font-weight-bold">Atualizado: </span>
          <?php echo $produto->atualizado_em->humanize(); ?>
        </p>
      <?php else: ?>
        <p class="card-text">
          <span class="font-weight-bold text-danger">Excluído: </span>
          <?php echo $produto->deletado_em->humanize(); ?>
        </p>
      <?php endif; ?>

      <div class="mt-4">

        <?php if ($produto->deletado_em == null): ?>
          <a href="<?php echo site_url("admin/produtos/editar/$produto->id"); ?>" class="btn btn-dark btn-sm mr-2">
            Editar
          </a>
          <a href="<?php echo site_url("admin/produtos/excluir/$produto->id"); ?>" class="btn btn-danger btn-sm mr-2">
            Excluir
          </a>
        <?php else: ?>
          <a href="<?php echo site_url("admin/produtos/desfazerexclusao/$produto->id"); ?>" class="btn btn-dark btn-sm">
            Desfazer
          </a>
        <?php endif; ?>

        <a href="<?php echo site_url("admin/produtos"); ?>" class="btn btn-light text-dark btn-sm mr-2">
          Voltar
        </a>
      </div>

    </div>
  </div>
</div>

<?php echo $this->endSection(); ?>

<!-- ----------------------------------------------- -->

<?php echo $this->section('scripts'); ?>

<?php echo $this->endSection(); ?>

<!-- ----------------------------------------------- -->