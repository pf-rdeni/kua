<?= $this->extend('backend/template/template'); ?>

<?= $this->section('content'); ?>
<div class="content-wrapper">
    <section class="content-header">
        <h1><?= isset($user) ? 'Edit' : 'Tambah' ?> User</h1>
    </section>

    <section class="content">
        <div class="container-fluid">
            <?php if (session()->has('errors')) : ?>
                <div class="alert alert-danger">
                    <ul><?php foreach (session('errors') as $error) : ?><li><?= esc($error) ?></li><?php endforeach ?></ul>
                </div>
            <?php endif ?>

            <div class="card card-primary">
                <form action="<?= isset($user) ? base_url('admin/users/update/' . $user->id) : base_url('admin/users/store') ?>" method="post">
                    <?= csrf_field() ?>
                    <div class="card-body">
                        <div class="form-group">
                            <label>Email *</label>
                            <input type="email" class="form-control" name="email" value="<?= old('email', $user->email ?? '') ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Username *</label>
                            <input type="text" class="form-control" name="username" value="<?= old('username', $user->username ?? '') ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label>Password <?= isset($user) ? '(Kosongkan jika tidak ubah)' : '*' ?></label>
                            <input type="password" class="form-control" name="password" <?= isset($user) ? '' : 'required' ?> autocomplete="off">
                        </div>
                        <div class="form-group">
                            <label>Konfirmasi Password <?= isset($user) ? '(Kosongkan jika tidak ubah)' : '*' ?></label>
                            <input type="password" class="form-control" name="pass_confirm" <?= isset($user) ? '' : 'required' ?> autocomplete="off">
                        </div>

                         <!-- Role Selection - Optional for now -->
                         <?php if (!isset($user) || (isset($user) && user_id() != $user->id)): ?>
                         <div class="form-group">
                            <label>Role</label>
                            <select class="form-control" name="role" required>
                                <option value="" disabled selected>-- Pilih Role --</option>
                                <?php if (isset($groups)): ?>
                                    <?php foreach ($groups as $group): ?>
                                        <?php 
                                            // Check if user has this role
                                            $isSelected = false;
                                            if (isset($userGroups)) {
                                                foreach ($userGroups as $ug) {
                                                    if ($ug['group_id'] == $group->id) {
                                                        $isSelected = true;
                                                        break;
                                                    }
                                                }
                                            }
                                        ?>
                                        <option value="<?= $group->id ?>" <?= $isSelected ? 'selected' : '' ?>>
                                            <?= esc($group->description ?? $group->name) ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                            <small class="text-muted">Pilih satu role untuk user ini.</small>
                         </div>
                         <?php endif; ?>

                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">Simpan</button>
                        <a href="<?= base_url('admin/users') ?>" class="btn btn-default float-right">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </section>
</div>
<?= $this->endSection(); ?>
