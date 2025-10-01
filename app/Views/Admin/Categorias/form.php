<div class="form-row">
   <div class="form-group col-md-12">
      <label for="nome">Nome</label>
      <input type="text" class="form-control" id="nome" name="nome" value="<?php echo old('nome', esc($categoria->nome)); ?>">
   </div>
</div>

<div class="form-row mb-2">
   <div class="form-check form-check-flat form-check-primary col-md-3">
      <label for="ativo" class="form-check-label">
         <input type="hidden" name="ativo" id="ativo" value="0">
         <input type="checkbox" name="ativo" id="ativo" value="1" <?php if (old('ativo', $categoria->ativo)): ?> checked <?php endif; ?>>
         Ativo
      </label>
   </div>
</div>

<button type="submit" class="btn btn-primary btn-sm mr-2">
   <i class="mdi mdi-checkbox-marked-circle btn-icon-prepend"></i>
   Salvar
</button>