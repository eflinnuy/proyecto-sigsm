<?php
/** Acceso a datos para la gestión administrativa de cuentas de funcionarios. */
class FuncionarioDAO {
    public function __construct(private PDO $pdo) {
    }

    public function listar(string $busqueda = '') : array {
        $sql = 'SELECT id,nombre,usuario,rol,activo FROM usuarios';
        if ($busqueda !== '') {
            $stmt = $this->pdo->prepare($sql.' WHERE nombre LIKE ? OR usuario LIKE ? ORDER BY nombre');
            $stmt->execute(['%'.$busqueda.'%', '%'.$busqueda.'%']);
        } else {
            $stmt = $this->pdo->query($sql.' ORDER BY nombre');
        }
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function buscar(int $id) : ?array {
        $stmt = $this->pdo->prepare('SELECT id,nombre,usuario,rol,activo FROM usuarios WHERE id=?');
        $stmt->execute([$id]);
        $funcionario = $stmt->fetch(PDO::FETCH_ASSOC);
        return $funcionario ?: null;
    }

    public function crear(string $nombre, string $usuario, string $clave, string $rol) : void {
        $stmt = $this->pdo->prepare('INSERT INTO usuarios(nombre,usuario,clave,rol,activo) VALUES(?,?,?,?,1)');
        $stmt->execute([$nombre, $usuario, password_hash($clave, PASSWORD_DEFAULT), $rol]);
    }

    public function actualizar(int $id, string $nombre, string $usuario, string $rol, ?string $clave = null) : void {
        if ($clave !== null && $clave !== '') {
            $stmt = $this->pdo->prepare('UPDATE usuarios SET nombre=?,usuario=?,rol=?,clave=? WHERE id=?');
            $stmt->execute([$nombre, $usuario, $rol, password_hash($clave, PASSWORD_DEFAULT), $id]);
            return;
        }
        $stmt = $this->pdo->prepare('UPDATE usuarios SET nombre=?,usuario=?,rol=? WHERE id=?');
        $stmt->execute([$nombre, $usuario, $rol, $id]);
    }

    public function cambiarEstado(int $id, bool $activo) : void {
        $stmt = $this->pdo->prepare('UPDATE usuarios SET activo=? WHERE id=?');
        $stmt->execute([$activo ? 1 : 0, $id]);
    }

    public function administradoresActivos() : int {
        return (int)$this->pdo->query("SELECT COUNT(*) FROM usuarios WHERE rol='admin' AND activo=1")->fetchColumn();
    }
}
