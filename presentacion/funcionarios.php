<?php
/** Alta, modificación y baja lógica de cuentas del personal. */
require_once __DIR__.'/../logica/funciones.php';
soloPersonal();
require_once __DIR__.'/../datos/conexion.php';
require_once __DIR__.'/../datos/FuncionarioDAO.php';

$dao = new FuncionarioDAO($pdo);
$roles = ['admin', 'usuario'];
$idSesion = (int)$_SESSION['usuario_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validarCsrf();
    $accion = $_POST['accion'] ?? '';
    $id = filter_var($_POST['id'] ?? '', FILTER_VALIDATE_INT) ?: 0;

    try {
        if ($accion === 'guardar') {
            $nombre = trim($_POST['nombre'] ?? '');
            $usuario = trim($_POST['usuario'] ?? '');
            $clave = $_POST['clave'] ?? '';
            $rol = $_POST['rol'] ?? '';
            $esEdicion = $id > 0;

            if (mb_strlen($nombre) < 2 || mb_strlen($nombre) > 100) {
                throw new InvalidArgumentException('El nombre debe tener entre 2 y 100 caracteres.');
            }
            if (!preg_match('/^[A-Za-z0-9._-]{3,50}$/', $usuario)) {
                throw new InvalidArgumentException('El usuario debe tener entre 3 y 50 caracteres: letras, números, punto, guion o guion bajo.');
            }
            if (!in_array($rol, $roles, true)) {
                throw new InvalidArgumentException('Seleccione un rol válido.');
            }
            if (!$esEdicion && strlen($clave) < 8) {
                throw new InvalidArgumentException('La contraseña inicial debe tener al menos 8 caracteres.');
            }
            if ($esEdicion && $clave !== '' && strlen($clave) < 8) {
                throw new InvalidArgumentException('La nueva contraseña debe tener al menos 8 caracteres.');
            }

            $actual = $esEdicion ? $dao->buscar($id) : null;
            if ($esEdicion && !$actual) {
                throw new InvalidArgumentException('No se encontró el funcionario seleccionado.');
            }
            if ($esEdicion && $id === $idSesion && $rol !== 'admin') {
                throw new InvalidArgumentException('No puedes quitarte tu propio acceso de administrador.');
            }
            if ($esEdicion && $actual['activo'] && $actual['rol'] === 'admin' && $rol !== 'admin' && $dao->administradoresActivos() <= 1) {
                throw new InvalidArgumentException('Debe quedar al menos un administrador activo.');
            }

            if ($esEdicion) {
                $dao->actualizar($id, $nombre, $usuario, $rol, $clave === '' ? null : $clave);
                if ($id === $idSesion) $_SESSION['nombre'] = $nombre;
                mensaje('Funcionario actualizado correctamente.');
            } else {
                $dao->crear($nombre, $usuario, $clave, $rol);
                mensaje('Funcionario creado correctamente.');
            }
            header('Location: funcionarios.php');
            exit;
        }

        if ($accion === 'estado') {
            $funcionario = $dao->buscar($id);
            if (!$funcionario) throw new InvalidArgumentException('No se encontró el funcionario seleccionado.');
            $nuevoEstado = !(bool)$funcionario['activo'];
            if ($id === $idSesion && !$nuevoEstado) {
                throw new InvalidArgumentException('No puedes desactivar tu propia cuenta mientras tienes la sesión abierta.');
            }
            if (!$nuevoEstado && $funcionario['activo'] && $funcionario['rol'] === 'admin' && $dao->administradoresActivos() <= 1) {
                throw new InvalidArgumentException('Debe quedar al menos un administrador activo.');
            }
            $dao->cambiarEstado($id, $nuevoEstado);
            mensaje($nuevoEstado ? 'Funcionario activado.' : 'Funcionario desactivado.');
            header('Location: funcionarios.php');
            exit;
        }
        throw new InvalidArgumentException('La operación solicitada no es válida.');
    } catch (InvalidArgumentException $e) {
        mensaje($e->getMessage(), 'error');
        header('Location: funcionarios.php'.($id ? '?editar='.$id : ''));
        exit;
    } catch (PDOException $e) {
        if ($e->getCode() === '23000') {
            mensaje('Ese nombre de usuario ya está registrado.', 'error');
        } else {
            error_log('Error al gestionar funcionario: '.$e->getMessage());
            mensaje('No se pudo guardar el funcionario. Revise la conexión y vuelva a intentarlo.', 'error');
        }
        header('Location: funcionarios.php'.($id ? '?editar='.$id : ''));
        exit;
    }
}

$idEdicion = filter_var($_GET['editar'] ?? '', FILTER_VALIDATE_INT) ?: 0;
$edicion = $idEdicion ? $dao->buscar($idEdicion) : null;
if ($idEdicion && !$edicion) {
    mensaje('No se encontró el funcionario seleccionado.', 'error');
    header('Location: funcionarios.php');
    exit;
}
$busqueda = trim($_GET['q'] ?? '');
$funcionarios = $dao->listar($busqueda);
$titulo = 'Gestión de funcionarios';
require __DIR__.'/vistas/encabezado.php';
?>
<div class="cabecera">
    <div>
        <h1>Funcionarios</h1>
<p>Administra las cuentas que pueden ingresar al panel SIGSM.</p>
    </div>
</div>

<section class="tarjeta">
    <h2><?= $edicion ? 'Modificar funcionario' : 'Agregar funcionario' ?></h2>
    <p>La contraseña se guarda protegida. Al modificar una cuenta, déjala vacía para conservar la contraseña actual.</p>
    <form method="post" autocomplete="off">
        <?= campoCsrf() ?>
        <input type="hidden" name="accion" value="guardar">
        <input type="hidden" name="id" value="<?= (int)($edicion['id'] ?? 0) ?>">
        <label for="nombre">Nombre completo</label>
        <input id="nombre" name="nombre" maxlength="100" required value="<?= limpiar($edicion['nombre'] ?? '') ?>" autocomplete="name">
        <label for="usuario">Nombre de usuario</label>
        <input id="usuario" name="usuario" maxlength="50" pattern="[A-Za-z0-9._-]{3,50}" required value="<?= limpiar($edicion['usuario'] ?? '') ?>" autocomplete="username">
        <label for="clave"><?= $edicion ? 'Nueva contraseña (opcional)' : 'Contraseña inicial' ?></label>
        <input id="clave" name="clave" type="password" minlength="8" <?= $edicion ? '' : 'required' ?> autocomplete="new-password" placeholder="Mínimo 8 caracteres">
        <label for="rol">Permiso</label>
        <select id="rol" name="rol" required>
            <option value="usuario" <?= ($edicion['rol'] ?? 'usuario') === 'usuario' ? 'selected' : '' ?>>Funcionario</option>
            <option value="admin" <?= ($edicion['rol'] ?? '') === 'admin' ? 'selected' : '' ?>>Administrador</option>
        </select>
        <div class="acciones-form">
            <button type="submit"><?= $edicion ? 'Guardar cambios' : 'Crear funcionario' ?></button>
            <?php if ($edicion): ?><a class="boton secundario" href="funcionarios.php">Cancelar</a><?php endif; ?>
        </div>
    </form>
</section>

<section class="tarjeta">
    <h2>Usuarios del panel</h2>
    <form method="get" class="buscar">
        <input name="q" value="<?= limpiar($busqueda) ?>" placeholder="Buscar por nombre o usuario" aria-label="Buscar funcionarios">
        <button type="submit">Buscar</button>
    </form>
    <div class="tabla-wrap">
        <table>
            <thead><tr><th>Nombre</th><th>Usuario</th><th>Permiso</th><th>Estado</th><th>Acciones</th></tr></thead>
            <tbody>
            <?php foreach ($funcionarios as $funcionario): ?>
                <tr>
                    <td><?= limpiar($funcionario['nombre']) ?></td>
                    <td><?= limpiar($funcionario['usuario']) ?></td>
                    <td><?= $funcionario['rol'] === 'admin' ? 'Administrador' : 'Funcionario' ?></td>
                    <td><?= $funcionario['activo'] ? 'Activo' : 'Inactivo' ?></td>
                    <td class="acciones-tabla">
                        <a class="boton secundario" href="funcionarios.php?editar=<?= (int)$funcionario['id'] ?>">Modificar</a>
                        <form method="post" onsubmit="return confirm('¿<?= $funcionario['activo'] ? 'Desactivar' : 'Activar' ?> esta cuenta?');">
                            <?= campoCsrf() ?>
                            <input type="hidden" name="accion" value="estado">
                            <input type="hidden" name="id" value="<?= (int)$funcionario['id'] ?>">
                            <button class="<?= $funcionario['activo'] ? 'peligro' : '' ?>" type="submit"><?= $funcionario['activo'] ? 'Desactivar' : 'Activar' ?></button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (!$funcionarios): ?><tr><td colspan="5">No se encontraron funcionarios.</td></tr><?php endif; ?>
            </tbody>
        </table>
    </div>
    <p class="ayuda">La baja desactiva la cuenta y conserva sus registros; la persona ya no podrá iniciar sesión.</p>
</section>
<?php require __DIR__.'/vistas/pie.php'; ?>
