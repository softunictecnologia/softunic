<div class="form-row">
   <div class="form-group col-md-4">
      <label for="nome">Nome</label>
      <input type="text" class="form-control" id="nome" name="nome" value="<?php echo old('nome', esc($usuario->nome)); ?>">
   </div>

   <div class="form-group col-md-2">
      <label for="nome">Cpf</label>
      <input type="text" class="form-control cpf" id="cpf" name="cpf" value="<?php echo old('cpf', esc($usuario->cpf)); ?>">
   </div>

   <div class="form-group col-md-3">
      <label for="nome">Telefone</label>
      <input type="text" class="form-control sp_celphones" id="telefone" name="telefone" value="<?php echo old('telefone', esc($usuario->telefone)); ?>">
   </div>

   <div class="form-group col-md-3">
      <label for="nome">E-mail</label>
      <input type="text" class="form-control" id="email" name="email" value="<?php echo old('email', esc($usuario->email)); ?>">
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
</div>

<div class="form-row">
   <div class="form-check form-check-flat form-check-primary col-md-3">
      <label for="is_admin" class="form-check-label">
         <input type="hidden" name="is_admin" id="is_admin" value="0">
         <input type="checkbox" name="is_admin" id="is_admin" value="1" <?php if (old('is_admin', $usuario->is_admin)): ?> checked <?php endif; ?>>
         Administrador
      </label>
   </div>

   <div class="form-check form-check-flat form-check-primary col-md-3">
      <label for="ativo" class="form-check-label">
         <input type="hidden" name="ativo" id="ativo" value="0">
         <input type="checkbox" name="ativo" id="ativo" value="1" <?php if (old('ativo', $usuario->ativo)): ?> checked <?php endif; ?>>
         Ativo
      </label>
   </div>
</div>

<button type="submit" class="btn btn-primary btn-sm mr-2">
   <i class="mdi mdi-checkbox-marked-circle btn-icon-prepend"></i>
   Salvar
</button>