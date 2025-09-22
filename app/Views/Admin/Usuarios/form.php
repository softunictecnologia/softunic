<div class="form-row">
   <div class="form-group col-md-4">
      <label for="nome">Nome</label>
      <input type="text" class="form-control" id="nome" name="nome" value="<?php echo esc($usuario->nome); ?>">
   </div>

   <div class="form-group col-md-2">
      <label for="nome">Cpf</label>
      <input type="text" class="form-control cpf" id="cpf" name="cpf" value="<?php echo esc($usuario->cpf); ?>">
   </div>

   <div class="form-group col-md-3">
      <label for="nome">Telefone</label>
      <input type="text" class="form-control sp_celphones" id="telefone" name="telefone" value="<?php echo esc($usuario->telefone); ?>">
   </div>

   <div class="form-group col-md-3">
      <label for="nome">E-mail</label>
      <input type="text" class="form-control" id="email" name="email" value="<?php echo esc($usuario->email); ?>">
   </div>
</div>

<div class="form-row">
   <div class="form-group col-md-3">
      <label for="nome">Senha</label>
      <input type="text" class="form-control" id="password" name="password">
   </div>

   <div class="form-group col-md-3">
      <label for="confirmation_password">Confirmação de senha</label>
      <input type="text" class="form-control" id="password_confirmation" name="password_confirmation">
   </div>

   <div class="form-group col-md-4">
      <label for="email">Perfil Acesso</label>
      <select class="form-control" name="is_admin" id="is_admin">
         <?php if ($usuario->id): ?>
            <option value="1" <?php echo ($usuario->is_admin ? 'selected' : ''); ?>>Administrador</option>
            <option value="0" <?php echo (!$usuario->is_admin ? 'selected' : ''); ?>>Cliente</option>
         <?php else: ?>
            <option value="1">Administrador</option>
            <option value="0" selected>Cliente</option>
         <?php endif; ?>
      </select>
   </div>

   <div class="form-group col-md-2">
      <label for="email">Ativo</label>
      <select class="form-control" name="ativo" id="ativo">
         <?php if ($usuario->id): ?>
            <option value="1" <?php echo ($usuario->ativo ? 'selected' : ''); ?>>Sim</option>
            <option value="0" <?php echo (!$usuario->ativo ? 'selected' : ''); ?>>Não</option>
         <?php else: ?>
            <option value="1">Sim</option>
            <option value="0" selected>Não</option>
         <?php endif; ?>
      </select>
   </div>
</div>

<button type="submit" class="btn btn-primary btn-sm mr-2">
   <i class="mdi mdi-checkbox-marked-circle btn-icon-prepend"></i>
   Salvar
</button>
<a href="<?php echo site_url("admin/usuarios/show/$usuario->id"); ?>" class="btn btn-light text-dark btn-sm">
   <i class="mdi mdi-arrow-left btn-icon-prepend"></i>
   Voltar
</a>