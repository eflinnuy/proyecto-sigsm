<?php
/**
 * Data Access Object (DAO) de usuarios.
 * La consulta devuelve solamente usuarios activos para el proceso
 * de autenticación.
 */
class UsuarioDAO {
    public function __construct(private PDO $pdo) {
    }
    public function buscarPorUsuario(string $usuario) : ? array {
        $s = $this->pdo->prepare('SELECT id,nombre,usuario,clave,rol FROM usuarios WHERE usuario=? AND activo=1 LIMIT 1');
        $s->execute([$usuario]);
        $r = $s->fetch(PDO::FETCH_ASSOC);
        return $r ?: null;
    }

    public function buscarIdPorUsuarioYCedula(string $usuario, string $cedula) : ?array {
        $stmt = $this->pdo->prepare('SELECT id FROM usuarios WHERE usuario=? AND cedula=? AND activo=1 LIMIT 1');
        $stmt->execute([$usuario, $cedula]);
        $cuenta = $stmt->fetch(PDO::FETCH_ASSOC);
        return $cuenta ?: null;
    }

    public function restablecerClavePorId(int $id, string $clave) : bool {
        $stmt = $this->pdo->prepare('UPDATE usuarios SET clave=? WHERE id=? AND activo=1');
        $stmt->execute([password_hash($clave, PASSWORD_DEFAULT), $id]);
        return $stmt->rowCount() === 1;
    }
}
