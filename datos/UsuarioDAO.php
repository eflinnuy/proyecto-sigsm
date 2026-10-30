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

    public function restablecerClavePorUsuarioYCedula(string $usuario, string $cedula, string $clave) : bool {
        $stmt = $this->pdo->prepare('UPDATE usuarios SET clave=? WHERE usuario=? AND cedula=? AND activo=1');
        $stmt->execute([password_hash($clave, PASSWORD_DEFAULT), $usuario, $cedula]);
        return $stmt->rowCount() === 1;
    }
}
