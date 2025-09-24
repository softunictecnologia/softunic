<?php echo $this->extend('Admin/layout/principal_autenticacao'); ?>

<!-- ----------------------------------------------- -->

<?php echo $this->section('titulo'); ?>
<?php echo $titulo; ?>
<?php echo $this->endSection(); ?>

<!-- ----------------------------------------------- -->

<?php echo $this->section('estilos'); ?>

<?php echo $this->endSection(); ?>

<!-- ----------------------------------------------- -->

<?php echo $this->section('conteudo'); ?>
<div class="container-fluid page-body-wrapper full-page-wrapper">
   <div class="content-wrapper d-flex align-items-center auth px-0">
      <div class="row w-100 mx-0">
         <div class="col-lg-4 mx-auto">
            <div class="auth-form-light text-left py-5 px-4 px-sm-5">

               <?php if (session()->has('sucesso')): ?>
                  <div class="alert alert-success alert-dismissible fade show" role="alert">
                     <strong>Perfeito!</strong> <?php echo session('sucesso'); ?>
                     <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                     </button>
                  </div>
               <?php endif; ?>

               <?php if (session()->has('info')): ?>
                  <div class="alert alert-info alert-dismissible fade show" role="alert">
                     <strong>Informação!</strong> <?php echo session('info'); ?>
                     <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                     </button>
                  </div>
               <?php endif; ?>

               <?php if (session()->has('atencao')): ?>
                  <div class="alert alert-danger alert-dismissible fade show" role="alert">
                     <strong>Atenção!</strong> <?php echo session('atencao'); ?>
                     <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                     </button>
                  </div>
               <?php endif; ?>

               <?php if (session()->has('error')): ?>
                  <div class="alert alert-danger alert-dismissible fade show" role="alert">
                     <strong>Erro!</strong> <?php echo session('error'); ?>
                     <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                     </button>
                  </div>
               <?php endif; ?>

               <div class="brand-logo">
                  <img src="<?php echo site_url('admin/') ?>images/logo.svg" alt="logo">
               </div>
               <h4>Olá, Seja bem vindo(a)!</h4>
               <h6 class="font-weight-light">Por favor realize o login.</h6>
               <?php echo form_open('login/criar'); ?>
               <div class="form-group">
                  <input type="email" class="form-control form-control-lg" name="email" value="<?php old('email'); ?>" id="exampleInputEmail1" placeholder="Digite seu e-mail">
               </div>
               <div class="form-group">
                  <input type="password" class="form-control form-control-lg" name="password" id="exampleInputPassword1" placeholder="Digite sua senha">
               </div>
               <div class="mt-3">
                  <button class="btn btn-block btn-primary btn-lg font-weight-medium auth-form-btn">ACESSAR</button>
               </div>

               <div class="text-center mt-4 font-weight-light">
                  Ainda não tem uma conta? <a href="<?php echo site_url('registrar') ?>" class="text-primary">Criar</a>
               </div>
               <?php form_close(); ?>
            </div>
         </div>
      </div>
   </div>
   <!-- content-wrapper ends -->
</div>
<?php echo $this->endSection(); ?>

<!-- ----------------------------------------------- -->

<?php echo $this->section('scripts'); ?>

<?php echo $this->endSection(); ?>

<!-- ----------------------------------------------- -->